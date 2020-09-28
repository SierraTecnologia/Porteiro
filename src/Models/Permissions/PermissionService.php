<?php namespace Porteiro\Models\Permissions;

use Porteiro\Models\Permissions;
use App\Models\Role;
use Population\Models\Components\Book\Book;
use Population\Models\Components\Book\Bookshelf;
use Population\Models\Components\Book\Chapter;
use Population\Models\Components\Book\Entity;
use Population\Models\Components\Book\EntityProvider;
use Population\Models\Components\Book\Page;
use Support\Models\Ownable;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

class PermissionService
{

    protected $currentAction;
    protected $isAdminUser;

    /**
     * @var false
     */
    protected bool $userRoles = false;
    protected $currentUserModel = false;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var JointPermission
     */
    protected $jointPermission;

    /**
     * @var Role
     */
    protected $role;

    /**
     * @var EntityPermission
     */
    protected $entityPermission;

    /**
     * @var EntityProvider
     */
    protected $entityProvider;

    /**
     * @var Collection[]|null
     *
     * @psalm-var array<array-key, Collection>|null
     */
    protected $entityCache;

    /**
     * Set the database connection
     *
     * @param Connection $connection
     *
     * @return void
     */
    public function setConnection(Connection $connection): void
    {
        $this->db = $connection;
    }

    /**
     * Prepare the local entity cache and ensure it's empty
     *
     * @param \Population\Models\Components\Book\Entity[] $entities
     *
     * @return void
     */
    protected function readyEntityCache($entities = []): void
    {
        $this->entityCache = [];

        foreach ($entities as $entity) {
            $type = $entity->getType();
            if (!isset($this->entityCache[$type])) {
                $this->entityCache[$type] = collect();
            }
            $this->entityCache[$type]->put($entity->id, $entity);
        }
    }

    /**
     * Get a book via ID, Checks local cache
     *
     * @param  $bookId
     * @return Book
     */
    protected function getBook($bookId)
    {
        if (isset($this->entityCache['book']) && $this->entityCache['book']->has($bookId)) {
            return $this->entityCache['book']->get($bookId);
        }

        $book = $this->entityProvider->book->find($bookId);
        if ($book === null) {
            $book = false;
        }

        return $book;
    }

    /**
     * Get a chapter via ID, Checks local cache
     *
     * @param  $chapterId
     * @return \Population\Models\Components\Book\Book
     */
    protected function getChapter($chapterId)
    {
        if (isset($this->entityCache['chapter']) && $this->entityCache['chapter']->has($chapterId)) {
            return $this->entityCache['chapter']->get($chapterId);
        }

        $chapter = $this->entityProvider->chapter->find($chapterId);
        if ($chapter === null) {
            $chapter = false;
        }

        return $chapter;
    }

    /**
     * Get the roles for the current user;
     *
     * @return array|bool
     */
    protected function getRoles()
    {
        if ($this->userRoles !== false) {
            return $this->userRoles;
        }

        $roles = [];

        if (auth()->guest()) {
            $roles[] = $this->role->getSystemRole('public')->id;
            return $roles;
        }


        foreach ($this->currentUser()->roles as $role) {
            $roles[] = $role->id;
        }
        return $roles;
    }

    /**
     * Re-generate all entity permission from scratch.
     *
     * @return void
     */
    public function buildJointPermissions(): void
    {
        $this->jointPermission->truncate();
        $this->readyEntityCache();

        // Get all roles (Should be the most limited dimension)
        $roles = $this->role->with('permissions')->get()->all();

        // Chunk through all books
        $this->bookFetchQuery()->chunk(
            5, function ($books) use ($roles) {
                $this->buildJointPermissionsForBooks($books, $roles);
            }
        );

        // Chunk through all bookshelves
        $this->entityProvider->bookshelf->newQuery()->select(['id', 'restricted', 'created_by'])
            ->chunk(
                50, function ($shelves) use ($roles) {
                    $this->buildJointPermissionsForShelves($shelves, $roles);
                }
            );
    }

    /**
     * Get a query for fetching a book with it's children.
     *
     * @return QueryBuilder
     */
    protected function bookFetchQuery()
    {
        return $this->entityProvider->book->newQuery()
            ->select(['id', 'restricted', 'created_by'])->with(
                ['chapters' => function ($query) {
                    $query->select(['id', 'restricted', 'created_by', 'book_id']);
                }, 'pages'  => function ($query) {
                    $query->select(['id', 'restricted', 'created_by', 'book_id', 'chapter_id']);
                }]
            );
    }

    /**
     * @param Collection $shelves
     * @param array      $roles
     * @param bool       $deleteOld
     *
     * @throws \Throwable
     *
     * @return void
     */
    protected function buildJointPermissionsForShelves($shelves, $roles, $deleteOld = false): void
    {
        if ($deleteOld) {
            $this->deleteManyJointPermissionsForEntities($shelves->all());
        }
        $this->createManyJointPermissions($shelves, $roles);
    }

    /**
     * Build joint permissions for an array of books
     *
     * @param Collection $books
     * @param array      $roles
     * @param bool       $deleteOld
     *
     * @throws \Throwable
     *
     * @return void
     */
    protected function buildJointPermissionsForBooks($books, $roles, $deleteOld = false): void
    {
        $entities = clone $books;

        /**
 * @var Book $book 
*/
        foreach ($books->all() as $book) {
            foreach ($book->getRelation('chapters') as $chapter) {
                $entities->push($chapter);
            }
            foreach ($book->getRelation('pages') as $page) {
                $entities->push($page);
            }
        }

        if ($deleteOld) {
            $this->deleteManyJointPermissionsForEntities($entities->all());
        }
        $this->createManyJointPermissions($entities, $roles);
    }

    /**
     * Rebuild the entity jointPermissions for a particular entity.
     *
     * @param \Population\Models\Components\Book\Entity $entity
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function buildJointPermissionsForEntity(Entity $entity)
    {
        $entities = [$entity];
        if ($entity->isA('book')) {
            $books = $this->bookFetchQuery()->where('id', '=', $entity->id)->get();
            $this->buildJointPermissionsForBooks($books, $this->role->newQuery()->get(), true);
            return;
        }

        if ($entity->book) {
            $entities[] = $entity->book;
        }

        if ($entity->isA('page') && $entity->chapter_id) {
            $entities[] = $entity->chapter;
        }

        if ($entity->isA('chapter')) {
            foreach ($entity->pages as $page) {
                $entities[] = $page;
            }
        }

        $this->buildJointPermissionsForEntities(collect($entities));
    }

    /**
     * Rebuild the entity jointPermissions for a collection of entities.
     *
     * @param Collection $entities
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function buildJointPermissionsForEntities(Collection $entities): void
    {
        $roles = $this->role->newQuery()->get();
        $this->deleteManyJointPermissionsForEntities($entities->all());
        $this->createManyJointPermissions($entities, $roles);
    }

    /**
     * Build the entity jointPermissions for a particular role.
     *
     * @param Role $role
     *
     * @return void
     */
    public function buildJointPermissionForRole(Role $role): void
    {
        $roles = [$role];
        $this->deleteManyJointPermissionsForRoles($roles);

        // Chunk through all books
        $this->bookFetchQuery()->chunk(
            20, function ($books) use ($roles) {
                $this->buildJointPermissionsForBooks($books, $roles);
            }
        );

        // Chunk through all bookshelves
        $this->entityProvider->bookshelf->newQuery()->select(['id', 'restricted', 'created_by'])
            ->chunk(
                50, function ($shelves) use ($roles) {
                    $this->buildJointPermissionsForShelves($shelves, $roles);
                }
            );
    }

    /**
     * Delete the entity jointPermissions attached to a particular role.
     *
     * @param Role $role
     *
     * @return void
     */
    public function deleteJointPermissionsForRole(Role $role): void
    {
        $this->deleteManyJointPermissionsForRoles([$role]);
    }

    /**
     * Delete all of the entity jointPermissions for a list of entities.
     *
     * @param Role[] $roles
     *
     * @return void
     */
    protected function deleteManyJointPermissionsForRoles($roles): void
    {
        $roleIds = array_map(
            function ($role) {
                return $role->id;
            }, $roles
        );
        $this->jointPermission->newQuery()->whereIn('role_id', $roleIds)->delete();
    }

    /**
     * Delete the entity jointPermissions for a particular entity.
     *
     * @param Entity $entity
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function deleteJointPermissionsForEntity(Entity $entity): void
    {
        $this->deleteManyJointPermissionsForEntities([$entity]);
    }

    /**
     * Delete all of the entity jointPermissions for a list of entities.
     *
     * @param \Population\Models\Components\Book\Entity[] $entities
     *
     * @throws \Throwable
     *
     * @return void
     */
    protected function deleteManyJointPermissionsForEntities($entities)
    {
        if (count($entities) === 0) {
            return;
        }

        $this->db->transaction(
            function () use ($entities) {

                foreach (array_chunk($entities, 1000) as $entityChunk) {
                    $query = $this->db->table('joint_permissions');
                    foreach ($entityChunk as $entity) {
                        $query->orWhere(
                            function (QueryBuilder $query) use ($entity) {
                                $query->where('entity_id', '=', $entity->id)
                                    ->where('entity_type', '=', $entity->getMorphClass());
                            }
                        );
                    }
                    $query->delete();
                }
            }
        );
    }

    /**
     * Create & Save entity jointPermissions for many entities and jointPermissions.
     *
     * @param Collection $entities
     * @param array      $roles
     *
     * @throws \Throwable
     *
     * @return void
     */
    protected function createManyJointPermissions($entities, $roles): void
    {
        $this->readyEntityCache($entities);
        $jointPermissions = [];

        // Fetch Entity Permissions and create a mapping of entity restricted statuses
        $entityRestrictedMap = [];
        $permissionFetch = $this->entityPermission->newQuery();
        foreach ($entities as $entity) {
            $entityRestrictedMap[$entity->getMorphClass() . ':' . $entity->id] = boolval($entity->getRawAttribute('restricted'));
            $permissionFetch->orWhere(
                function ($query) use ($entity) {
                    $query->where('restrictable_id', '=', $entity->id)->where('restrictable_type', '=', $entity->getMorphClass());
                }
            );
        }
        $permissions = $permissionFetch->get();

        // Create a mapping of explicit entity permissions
        $permissionMap = [];
        foreach ($permissions as $permission) {
            $key = $permission->restrictable_type . ':' . $permission->restrictable_id . ':' . $permission->role_id . ':' . $permission->action;
            $isRestricted = $entityRestrictedMap[$permission->restrictable_type . ':' . $permission->restrictable_id];
            $permissionMap[$key] = $isRestricted;
        }

        // Create a mapping of role permissions
        $rolePermissionMap = [];
        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                $rolePermissionMap[$role->getRawAttribute('id') . ':' . $permission->getRawAttribute('name')] = true;
            }
        }

        // Create Joint Permission Data
        foreach ($entities as $entity) {
            foreach ($roles as $role) {
                foreach ($this->getActions($entity) as $action) {
                    $jointPermissions[] = $this->createJointPermissionData($entity, $role, $action, $permissionMap, $rolePermissionMap);
                }
            }
        }

        $this->db->transaction(
            function () use ($jointPermissions) {
                foreach (array_chunk($jointPermissions, 1000) as $jointPermissionChunk) {
                    $this->db->table('joint_permissions')->insert($jointPermissionChunk);
                }
            }
        );
    }


    /**
     * Get the actions related to an entity.
     *
     * @param \Population\Models\Components\Book\Entity $entity
     *
     * @return string[]
     *
     * @psalm-return array{0: string, 1: string, 2: string, 3?: string, 4?: string}
     */
    protected function getActions(Entity $entity): array
    {
        $baseActions = ['view', 'update', 'delete'];
        if ($entity->isA('chapter') || $entity->isA('book')) {
            $baseActions[] = 'page-create';
        }
        if ($entity->isA('book')) {
            $baseActions[] = 'chapter-create';
        }
        return $baseActions;
    }

    /**
     * Create entity permission data for an entity and role
     * for a particular action.
     *
     * @param  Entity $entity
     * @param  Role   $role
     * @param  string $action
     * @param  array  $permissionMap
     * @param  array  $rolePermissionMap
     * @return array
     */
    protected function createJointPermissionData(Entity $entity, Role $role, $action, $permissionMap, $rolePermissionMap)
    {
        $permissionPrefix = (strpos($action, '-') === false ? ($entity->getType() . '-') : '') . $action;
        $roleHasPermission = isset($rolePermissionMap[$role->getRawAttribute('id') . ':' . $permissionPrefix . '-all']);
        $roleHasPermissionOwn = isset($rolePermissionMap[$role->getRawAttribute('id') . ':' . $permissionPrefix . '-own']);
        $explodedAction = explode('-', $action);
        $restrictionAction = end($explodedAction);

        if ($role->system_name === 'admin') {
            return $this->createJointPermissionDataArray($entity, $role, $action, true, true);
        }

        if ($entity->restricted) {
            $hasAccess = $this->mapHasActiveRestriction($permissionMap, $entity, $role, $restrictionAction);
            return $this->createJointPermissionDataArray($entity, $role, $action, $hasAccess, $hasAccess);
        }

        if ($entity->isA('book') || $entity->isA('bookshelf')) {
            return $this->createJointPermissionDataArray($entity, $role, $action, $roleHasPermission, $roleHasPermissionOwn);
        }

        // For chapters and pages, Check if explicit permissions are set on the Book.
        $book = $this->getBook($entity->book_id);
        $hasExplicitAccessToParents = $this->mapHasActiveRestriction($permissionMap, $book, $role, $restrictionAction);
        $hasPermissiveAccessToParents = !$book->restricted;

        // For pages with a chapter, Check if explicit permissions are set on the Chapter
        if ($entity->isA('page') && $entity->chapter_id !== 0 && $entity->chapter_id !== '0') {
            $chapter = $this->getChapter($entity->chapter_id);
            $hasPermissiveAccessToParents = $hasPermissiveAccessToParents && !$chapter->restricted;
            if ($chapter->restricted) {
                $hasExplicitAccessToParents = $this->mapHasActiveRestriction($permissionMap, $chapter, $role, $restrictionAction);
            }
        }

        return $this->createJointPermissionDataArray(
            $entity,
            $role,
            $action,
            ($hasExplicitAccessToParents || ($roleHasPermission && $hasPermissiveAccessToParents)),
            ($hasExplicitAccessToParents || ($roleHasPermissionOwn && $hasPermissiveAccessToParents))
        );
    }

    /**
     * Check for an active restriction in an entity map.
     *
     * @param  $entityMap
     * @param  Entity $entity
     * @param  Role   $role
     * @param  $action
     * @return bool
     */
    protected function mapHasActiveRestriction(array $entityMap, Entity $entity, Role $role, string $action)
    {
        $key = $entity->getMorphClass() . ':' . $entity->getRawAttribute('id') . ':' . $role->getRawAttribute('id') . ':' . $action;
        return isset($entityMap[$key]) ? $entityMap[$key] : false;
    }

    /**
     * Create an array of data with the information of an entity jointPermissions.
     * Used to build data for bulk insertion.
     *
     * @param \Population\Models\Components\Book\Entity $entity
     * @param Role                                      $role
     * @param $action
     * @param $permissionAll
     * @param $permissionOwn
     *
     * @return (bool|mixed|string)[]
     *
     * @psalm-return array{role_id: mixed, entity_id: mixed, entity_type: string, action: string, has_permission: bool, has_permission_own: bool, created_by: mixed}
     */
    protected function createJointPermissionDataArray(Entity $entity, Role $role, string $action, bool $permissionAll, bool $permissionOwn): array
    {
        return [
            'role_id'            => $role->getRawAttribute('id'),
            'entity_id'          => $entity->getRawAttribute('id'),
            'entity_type'        => $entity->getMorphClass(),
            'action'             => $action,
            'has_permission'     => $permissionAll,
            'has_permission_own' => $permissionOwn,
            'created_by'         => $entity->getRawAttribute('created_by')
        ];
    }

    /**
     * The general query filter to remove all entities
     * that the current user does not have access to.
     *
     * @param  $query
     * @return mixed
     */
    protected function entityRestrictionQuery($query)
    {
        $q = $query->where(
            function ($parentQuery) {
                $parentQuery->whereHas(
                    'jointPermissions', function ($permissionQuery) {
                        $permissionQuery->whereIn('role_id', $this->getRoles())
                            ->where('action', '=', $this->currentAction)
                            ->where(
                                function ($query) {
                                    $query->where('has_permission', '=', true)
                                        ->orWhere(
                                            function ($query) {
                                                $query->where('has_permission_own', '=', true)
                                                    ->where('created_by', '=', $this->currentUser()->id);
                                            }
                                        );
                                }
                            );
                    }
                );
            }
        );
        $this->clean();
        return $q;
    }

    /**
     * Get the children of a book in an efficient single query, Filtered by the permission system.
     *
     * @param  integer $book_id
     * @param  bool    $filterDrafts
     * @param  bool    $fetchPageContent
     * @return QueryBuilder
     */
    public function bookChildrenQuery($book_id, $filterDrafts = false, $fetchPageContent = false): QueryBuilder
    {
        $entities = $this->entityProvider;
        $pageSelect = $this->db->table('pages')->selectRaw($entities->page->entityRawQuery($fetchPageContent))
            ->where('book_id', '=', $book_id)->where(
                function ($query) use ($filterDrafts) {
                    $query->where('draft', '=', 0);
                    if (!$filterDrafts) {
                        $query->orWhere(
                            function ($query) {
                                $query->where('draft', '=', 1)->where('created_by', '=', $this->currentUser()->id);
                            }
                        );
                    }
                }
            );
        $chapterSelect = $this->db->table('chapters')->selectRaw($entities->chapter->entityRawQuery())->where('book_id', '=', $book_id);
        $query = $this->db->query()->select('*')->from($this->db->raw("({$pageSelect->toSql()} UNION {$chapterSelect->toSql()}) AS U"))
            ->mergeBindings($pageSelect)->mergeBindings($chapterSelect);

        // Add joint permission filter
        $whereQuery = $this->db->table('joint_permissions as jp')->selectRaw('COUNT(*)')
            ->whereRaw('jp.entity_id=U.id')->whereRaw('jp.entity_type=U.entity_type')
            ->where('jp.action', '=', 'view')->whereIn('jp.role_id', $this->getRoles())
            ->where(
                function ($query) {
                    $query->where('jp.has_permission', '=', 1)->orWhere(
                        function ($query) {
                            $query->where('jp.has_permission_own', '=', 1)->where('jp.created_by', '=', $this->currentUser()->id);
                        }
                    );
                }
            );
        $query->whereRaw("({$whereQuery->toSql()}) > 0")->mergeBindings($whereQuery);

        $query->orderBy('draft', 'desc')->orderBy('priority', 'asc');
        $this->clean();
        return  $query;
    }

    /**
     * Add restrictions for a generic entity
     *
     * @param  string                                            $entityType
     * @param  Builder|\Population\Models\Components\Book\Entity $query
     * @param  string                                            $action
     * @return Builder
     */
    public function enforceEntityRestrictions($entityType, $query, $action = 'view')
    {
        if (strtolower($entityType) === 'page') {
            // Prevent drafts being visible to others.
            $query = $query->where(
                function ($query) {
                    $query->where('draft', '=', false);
                    if ($this->currentUser()) {
                        $query->orWhere(
                            function ($query) {
                                $query->where('draft', '=', true)->where('created_by', '=', $this->currentUser()->id);
                            }
                        );
                    }
                }
            );
        }

        $this->currentAction = $action;
        return $this->entityRestrictionQuery($query);
    }

    /**
     * Get the current user
     *
     * @return \App\Models\User
     */
    private function currentUser()
    {
        if ($this->currentUserModel === false) {
            $this->currentUserModel = user();
        }

        return $this->currentUserModel;
    }

    /**
     * Clean the cached user elements.
     *
     * @return void
     */
    private function clean(): void
    {
        $this->currentUserModel = false;
        $this->userRoles = false;
        $this->isAdminUser = null;
    }
}
