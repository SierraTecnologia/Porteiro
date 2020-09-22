<?php

namespace Porteiro\Http\Controllers\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
use App\Http\Requests;
use App\Http\Requests\BillingRequest;
use Porteiro\Http\Controllers\User\Controller;

class BillingController extends Controller
{
    /**
     * Billing selection
     *
     * @return \Illuminate\Http\Response
     */
    public function getSubscribe(Request $request)
    {
        $user = $request->user();
        $invoice = $user->meta->upcomingInvoice();

        if ($user->meta->subscribed(\Illuminate\Support\Facades\Config::get('plans.subscription_name')) && ! is_null($invoice)) {
            return view('billing.details')
                ->with('invoice', $invoice)
                ->with('invoiceDate', Carbon::createFromTimestamp($invoice->date))
                ->with('user', $user);
        }

        return view('billing.subscribe')
            ->with('user', $user);
    }

    /**
     * Create a subscription
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function postSubscribe(BillingRequest $request)
    {
        try {
            $payload = $request->all();
            $creditCardToken = $payload['sitecpaymentToken'];
            auth()->user()->meta->newSubscription(\Illuminate\Support\Facades\Config::get('plans.subscription_name'), \Illuminate\Support\Facades\Config::get('plans.subscription'))->create($creditCardToken);
            return redirect('user/billing/details')->with('message', 'You\'re now subscribed!');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back()->withErrors(['Could not process the billing please try again.']);
        }

        return back()->withErrors(['Could not complete billing, please try again.']);
    }

    /**
     * change a credit card
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function getChangeCard(Request $request)
    {
        $user = $request->user();

        return view('billing.change-card')
            ->with('user', $user);
    }

    /**
     * Save new credit card
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function postChangeCard(BillingRequest $request)
    {
        try {
            $payload = $request->all();
            $creditCardToken = $payload['sitecpaymentToken'];
            auth()->user()->meta->updateCard($creditCardToken);
            return redirect('user/billing/details')->with('message', 'Your subscription has been updated!');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back()->withErrors(['Could not process the billing please try again.']);
        }

        return back()->withErrors(['Could not complete billing, please try again.']);
    }

    /**
     * Add a coupon
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function getCoupon(Request $request)
    {
        $user = $request->user();

        return view('billing.coupon')
            ->with('user', $user);
    }

    /**
     * Use a coupon
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCoupon(BillingRequest $request)
    {
        try {
            $payload = $request->all();
            auth()->user()->meta->applyCoupon($payload['coupon']);
            return redirect('user/billing/details')->with('message', 'Your coupon was used!');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back()->withErrors(['Could not process the coupon please try again.']);
        }

        return back()->withErrors(['Could not add your coupon, please try again.']);
    }

    /**
     * Get invoices
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function getInvoices(Request $request)
    {
        $user = $request->user();
        $invoices = $user->meta->invoices(\Illuminate\Support\Facades\Config::get('plans.subscription_name'));

        return view('billing.invoices')
            ->with('invoices', $invoices)
            ->with('user', $user);
    }

    /**
     * Get one invoice
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function getInvoiceById($id, Request $request)
    {
        try {
            $user = $request->user();
            $response = $user->meta->downloadInvoice(
                $id, [
                'vendor'    => \Illuminate\Support\Facades\Config::get("invoice.company"),
                'street'    => \Illuminate\Support\Facades\Config::get("invoice.street"),
                'location'  => \Illuminate\Support\Facades\Config::get("invoice.location"),
                'phone'     => \Illuminate\Support\Facades\Config::get("invoice.phone"),
                'url'       => \Illuminate\Support\Facades\Config::get("invoice.url"),
                'product'   => \Illuminate\Support\Facades\Config::get("invoice.product"),
                'description'   => 'Subscription',
                ]
            );
        } catch (Exception $e) {
            $response = back()->withErrors(['Could not find this invoice, please try again.']);
        }

        return $response;
    }

    /**
     * Cancel Subscription
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function cancelSubscription(Request $request)
    {
        try {
            $user = $request->user();
            $invoice = $user->meta->upcomingInvoice();
            $date = Carbon::createFromTimestamp($invoice->date);
            $user->meta->subscription(\Illuminate\Support\Facades\Config::get('plans.subscription_name'))->cancel();
            return redirect('user/billing/details')->with('message', 'Your subscription has been cancelled! It will be availale until '.$date);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back()->withErrors(['Could not process the cancellation please try again.']);
        }

        return back()->withErrors(['Could not cancel billing, please try again.']);
    }
}
