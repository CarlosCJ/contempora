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

    public function validar($data){
        $mensaje = [];
        $mensaje = collect($mensaje);
        if (!Arr::exists($data, 'nombre')){
            $nombre = collect(['nombre' => 'No puede estar en blanco.']);
            $mensaje = $nombre;
        }
        if (!Arr::exists($data, 'email')){
            $email = collect(['correo' => 'No puede estar en blanco.']);
            $mensaje = $mensaje->merge($email);

        } else if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $email = collect(['correo' => 'Debe ser un correo valido.']);
            $mensaje = $mensaje->merge($email);
        }
        if (!Arr::exists($data, 'genero')){
            $genero = collect(['genero' => 'No puede estar en blanco, puede ser male o female']);
            $mensaje = $mensaje->merge($genero);
        }
        if (!Arr::exists($data, 'estado')){
            $genero = collect(['estado' => 'No puede estar en blanco, puede ser true o false']);
            $mensaje = $mensaje->merge($genero);
        }
        dd($mensaje);
        return $mensaje;
    }

    public function crearUsuario(Request $request){
        $dataEntrada = $request->input();
        $msj = $this->validar($dataEntrada);
        if($msj->count() != 0){
            return $msj;
        }
        $token = '40adc5ce702bf6220a5fcb1f97b4011b0583ed15f667fcd072350aeefe2035cf';
        $cli = new Client();
        $peticion = $cli->request('POST', 'https://gorest.co.in/public/v2/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
            'form_params' => [
                'name'      => $request->input('nombre'),
                'email'     => $request->input('email'),
                'gender'    => $request->input('genero'),
                'status'    => $request->input('estado') === 'true' ? 'active' : 'inactive',
            ]
        ]);
        // return ['result' => 'Data has been saved.'];
        return json_decode($peticion->getBody()->getContents());
        // return json_decode($request);
    }

    public function updateUser($id, Request $request){
        $dataEntrada = $request->input();
        $msj = $this->validar($dataEntrada);
        if($msj->count() != 0){
            return $msj;
        }
        $token = '40adc5ce702bf6220a5fcb1f97b4011b0583ed15f667fcd072350aeefe2035cf';
        $cli = new Client();
        $peticion = $cli->request('PUT', 'https://gorest.co.in/public/v2/users/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
            'json' => [
                'name'      => $request->input('nombre'),
                'email'     => $request->input('email'),
                'gender'    => $request->input('genero'),
                'status'    => $request->input('estado') === 'true' ? 'active' : 'inactive',
            ]
        ]);
        //dd($peticion);
        // return ['result' => 'Data has been saved.'];
        return json_decode($peticion->getBody()->getContents());
    }

    public function updateUserEmail(Request $request){
        dd($request);
        if ($request->hasAny(['email'])){
            $token = '40adc5ce702bf6220a5fcb1f97b4011b0583ed15f667fcd072350aeefe2035cf';
            $cli = new Client();
            $peticion = $cli->request('PUT', 'https://gorest.co.in/public/v2/users', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'email' => $request
                ]
                'json' => [
                    'name'      => $request->input('nombre'),
                    'email'     => $request->input('email'),
                    'gender'    => $request->input('genero'),
                    'status'    => $request->input('estado') === 'true' ? 'active' : 'inactive',
                ]
            ]);
        }
        dd($peticion);
        return ['result' => 'Data has been saved.'];
        return json_decode($peticion->getBody()->getContents());
    }
}