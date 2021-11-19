<?php

namespace App\Http\Controllers;

use App\Mail\invitacionMail;
use App\Models\Evento;
use App\Models\Invitado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class InvitadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Evento $evento)
    {
        Auth::user()->setEventoActual($evento);
        $invitados=$evento->invitados;
        return view('invitado/invitadoIndex',compact('invitados','evento'));
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
        Auth::user()->setEventoActual($request->evento_id);
        $request->validate([
            'nombre_invitado' => 'required',
            'correo_invitado' => 'required|email',
            'numero_boletos' => 'required|integer'

        ]);

        $invitado=Invitado::create($request->all());
        $evento=$invitado->evento;
        return redirect()->route('index_invitados',$evento);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invitado  $invitado
     * @return \Illuminate\Http\Response
     */
    public function show(Invitado $invitado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invitado  $invitado
     * @return \Illuminate\Http\Response
     */
    public function edit(Invitado $invitado)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invitado  $invitado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invitado $invitado)
    {
        $request->validate([
            'confirmacion' => 'required'

        ]);
        Invitado::where('id',$invitado->id)->update($request->except('_token','_method'));
        return redirect()->back()->with('success','Se ha enviado la confirmacion');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invitado  $invitado
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invitado $invitado)
    {
        $evento=$invitado->evento;
        $invitado->delete();
        return redirect()->route('index_invitados',$evento);
    }
    public function enviarInvitacion(Evento $evento, Invitado $invitado){
        mail::to($invitado->correo_invitado)->send(new invitacionMail($evento,$invitado));
        return redirect()->back();
    }
}
