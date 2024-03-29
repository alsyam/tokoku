<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardAdminProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('verified');
    }

    public function index()
    {
        // for CITY
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.rajaongkir.com/starter/city",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: 8717a7a6273120d5a841a3bf52623cc7"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $city = json_decode($response);
        }
        return view('dashboard.admin.index', [
            'admins' => User::where('id', Auth()->user()->id)->get(),
            'cities' => $city->rajaongkir->results,

        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.rajaongkir.com/starter/province",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: 8717a7a6273120d5a841a3bf52623cc7"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            //   echo $response;
            $province = json_decode($response);
        }

        return view('dashboard.admin.show', [
            'admins' => User::where('id', Auth()->user()->id)->first(),
            'provinces' => $province->rajaongkir->results,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // $rules = $request->validate([
        //     'name' => 'required|max:255',
        //     'username' => 'required|min:3|max:255',
        //     'email' => 'required|email:dns',
        //     'address' =>  'required|max:255',
        //     'province' =>  'required|max:255',
        //     'city' =>  'required|max:255',
        //     'zip_code' =>  'required|max:255',
        //     'phone_number' =>  'required|max:255',
        // ]);
        $member = User::where('id', Auth()->user()->id)->first();

        $admin['name'] = $request->get('name');
        $admin['username'] = $request->get('username');
        $admin['email'] = $request->get('email');
        $admin['phone_number'] = $request->get('phone_number');
        $admin['address'] = $request->get('address');
        $admin['province'] = $request->get('province');
        $admin['city'] = $request->get('city');
        $admin['zip_code'] = $request->get('zip_code');


        User::where('id', $member->id)->update($admin);

        return redirect('/dashboard/admins')->with('success', 'Admin Profile has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
