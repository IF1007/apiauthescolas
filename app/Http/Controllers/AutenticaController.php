<?php

namespace App\Http\Controllers;
use App\Usuario as Model;
use Illuminate\Http\Request;

class AutenticaController extends Controller
{
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function login(){
        $response = app('request')->all();
        $login = $response['login'];
        $senha = $response['senha'];
        if($login && $senha){
            $usuario = $this->model->where('login',$login)->get()->first();
            if($usuario && $usuario->senha == $senha){
                $token = (object)[
                    'id' => $usuario->id,
                    'periodo_id' => $usuario->id_periodo,
                    'curso_id' => $usuario->id_curso,
                    'date' => date("Y-m-d")
                ];
                $usuario->token = md5(json_encode($token));
                $usuario->save();
                return response()->json([
                    'aluno_id' => $usuario->id,
                    'token' => md5(json_encode($token))
                ]);
            }
            return response()->json([
                'error' => 'Login/senha inválido'
            ]);
        }
        return response()->json([
            'error' => 'Login e senha são obrigatórios'
        ]);
    }

    public function registrar(){
        $response = app('request')->all();
        $login = $response['login'];
        $senha = $response['senha'];
        if($login && $senha && $response['id_curso'] && $response['id_periodo']){
            if($this->model->where('login',$login)->get()->isEmpty()){
                $usuario = new $this->model;
                $usuario->login = $login;
                $usuario->senha = $senha;
                $usuario->id_curso = $response['id_curso'];
                $usuario->id_periodo = $response['id_periodo'];
                $usuario->token = null;
                $usuario->save();
                return response()->json($usuario);
            }
            return response()->json([
                'message' => 'Usuário já cadastrado'
            ]);
        }
        return response()->json([
            'message' => 'Parâmetros insuficientes'
        ]);
    }
}
