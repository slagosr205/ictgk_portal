<div>
   
    <!-- The whole future lies in uncertainty: live immediately. - Seneca -->
    <div class="row">
        @php
           use Jenssegers\Date\Date;
           Date::setLocale('es');
            
            $empresaNombre='';
            $identidad='';
        @endphp
        <div class="col-md">
        <div class="table-responsive">
        <table class="table table-striped " id="tbhistoricoempresa">
            <thead>
                <th>Empresa</th>
                <th>Puesto</th>
                <th>Forma de Egreso</th>
                <th>Fecha de Ingreso</th>
                <th>Fecha de Egreso</th>
                <th>Comentarios</th>
                <th>Area</th>
                <th>Estado</th>
            </thead>
            <tbody class="table-group-divider">
                
                  @if (count($informacionLaboral)>0)
                    @foreach ($informacionLaboral as $il)
                    @foreach ($datosEmpresa as $em)
                        @if ($em->id==$il['id_empresa'])
                        @php
                           $empresaNombre= $em->nombre;
                        @endphp
                            
                        @endif
                    @endforeach
                   
                    <tr>
                        @php
                        $fechaFormateada=null;
                        $fechaFormateadoEgreso=null;
                            if($il['fechaIngreso']!==null )
                            {
                                $fechaIngreso = Date::createFromFormat('Y-m-d', $il['fechaIngreso']);
                                $fechaFormateada = $fechaIngreso->format('l j F Y');

                                
                            }

                            if($il['fechaEgreso']!==null)
                            {
                                $fechaEgreso = Date::createFromFormat('Y-m-d', $il['fechaEgreso']);
                                $fechaFormateadoEgreso=$fechaEgreso->format('l j F Y');
                            }

                           
                        @endphp
                        <td>{{$empresaNombre}}</td>
                        <td>{{$il['nombrepuesto']}}</td>
                        <td>{{$il['forma_egreso']}}</td>
                        
                        <td>{{$fechaFormateada}}</td>
                        @if ($fechaFormateadoEgreso!=null)
                            <td>{{ $fechaFormateadoEgreso}}</td>  
                        @else
                            <td></td>
                        @endif
                        <td>{{$il['Comentario']}}</td>
                        <td>{{$il['area']}}</td>
                        @if ($il['recomendado'] === null)
                            <td style="text-align: center; vertical-align: middle;" id="elemento_{{$il->id}}">
                                
                                <i class="ri-user-heart-fill" style="font-size: 32px;color:#d11856" data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-custom-class="custom-tooltip"
                                data-bs-title="This top tooltip is themed via CSS variables." id="colaboradoractivo"></i>
                            </td>
                        @else
                            @if ($il['recomendado'] === 's')
                                <td style="text-align: center; vertical-align: middle;" id="elemento_{{$il->id}}">
                                    <i class="ri-check-double-line" style="font-size: 32px;color:#13753a"></i>
                                </td>
                            @else
                               @foreach ($perfil as $pu)
                                @if ($pu->perfilesdescrip==='admin')
                                    <td style="text-align: center; vertical-align: middle;" class="cambiarestado">
                                        <button class="btn btndesbloqueoRecomendacion" data-bs-toggle="modal" data-bs-target="#modaldebloqueoRecomendacion" >
                                            <i class="ri-information-line" style="font-size: 32px;color:#d6c73d"></i>
                                        </button>
                                    </td>
                                @else
                                    <td style="text-align: center; vertical-align: middle;" class="cambiarestado">
                                       
                                        <i class="ri-information-line" style="font-size: 32px;color:#d6c73d"></i>
                                       
                                    </td>
                                @endif
                                   
                               @endforeach
                                
                            @endif
                        @endif
                        
                    </tr>
                    
                    @php
                        $identidad=$il->identidad;
                        
                    @endphp

                    @endforeach
                  @endif  
                
                   
            
            </tbody>
        </table>
    </div>
        </div>
    </div>
    </div>

    <x-enviar-solicitud :identidad="$identidad" />
</div>