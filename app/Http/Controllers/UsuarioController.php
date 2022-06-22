<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp\Client;

use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;


class UsuarioController extends Controller
{
    public function getUsers(Request $request) {
        try {
            define('QUERYPARAMETERS', $request->query());
            if ($request->hasAny(['nombre', 'email', 'estado']) || QUERYPARAMETERS == null){
                $lista = $this->buscarData(QUERYPARAMETERS);
                if(QUERYPARAMETERS != null){
                    $collection = collect($lista);
                    $filtered = $collection->filter(function ($values, $key) use($request) {
                        [$itemName,$valueName] =  Arr::divide($request->all());
                        $itemName = $itemName[0];
                        $valueName = $valueName[0];
                        return $values[$itemName] == $valueName;
                    });
                    $datos = $filtered->all();
                    // return collect($datos);
                    return json_decode(json_encode($datos));
                } else {
                    return $lista;
                }
            } else {
                return ['mensaje' => 'Query string parameters no permitida'];
            }
        // } catch (\Throwable $th) {
        } catch (Exception $e) {
            return ['Error: ' => $e->getMessage()];
        }
    }

    public function buscarData($queryString){
        // dd($queryString);
        $token = '40adc5ce702bf6220a5fcb1f97b4011b0583ed15f667fcd072350aeefe2035cf';
        $client = new Client();
        // $respuesta = $client->request('GET', 'https://gorest.co.in/public/v2/users', [
        $respuesta = $client->request('GET', 'https://gorest.co.in/public/v2/users', [
            //'auth' => ['user', null],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ],
        ]);
        $usersList = json_decode($respuesta->getBody()->getContents());
        $data = collect($usersList);
        $datosKeySpanish = $data->map(function ($item){
        // $datosKeySpanish = $data->map(callback:function ($item){
            return [
                'id'            => $item->id,
                'nombre'        => $item->name,
                'email'         => $item->email,
                'genero'        => $item->gender,
                'estado'        => $item->status === 'active' ? 'true' : 'false'
            ];
        });
        //dd($datosKeySpanish);
        //dd(gettype($datosKeySpanish));
        return $datosKeySpanish;
    }


}