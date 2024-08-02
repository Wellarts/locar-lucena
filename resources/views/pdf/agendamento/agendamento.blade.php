<!DOCTYPE html>
<html>
<head>

<style>
    .retangulo {
        width: 100%;
        height: 2.5%;
        background-color: rgb(222, 225, 226);
        display: flex;
        align-items: center;
        text-align: center;
    }
    .texto {
        margin: auto;
        font-weight: bold;
        font-size: 20px;

    }
    .tabelas {
        border: 1px;
        border-style: solid;
        border-color: grey;
        width: 100%;
        border-collapse: collapse;
    }


    #ficha td {
    border: 1px solid rgb(160 160 160);
    padding: 8px 10px;
    }

    .tx {
        line-height:1.5;
    }

</style>

<style>
    .tela {
        width: 100%;
        height: 100px;
        border: 0px solid black;
        float: left;
        text-align: center;
        margin-top: 10%;



    }
</style>

</head>
<body>

<table style="width: 100%">
    <tr>
      <td><img src="{{ asset('img/logo-lucena.jpg') }}" alt="Image" height="30" width="90"></td>
      <td> <p style="width: 100%; font-size:28px; font-weight: bold;" align="center">Locadora Lucena</p>
           <p style="font-size:16px;" align="center">Av. Cesário de Melo, nº 4030 Campo Grande - Rio de Janeiro - RJ.<br>
                                                    Contato: (21)7402-1183<br>
                                                    Email: erike@rdbled.com.br - CNPJ: 53-825-708/0001-48</p>
      </td>
  </tr>

  </table>
    <div class="retangulo">
        <span class="texto">FICHA DE AGENDAMENTO</span>
    </div>
<table>
</table>
<div class="retangulo">
    <span class="texto">LOCATÁRIO</span>
</div>

<table class="tabelas" width="100%"  id='ficha'>
    <tr>
        <td colspan="2">
            <b class="tx">Nome:</b> {{$agendamento->Cliente->nome}}</p>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b class="tx">Endereço:</b>  {{$agendamento->Cliente->endereco}}
        </td>
    <tr>
        <td>
            <b class="tx">Cidade:</b> {{$agendamento->Cliente->Cidade->nome}}
        </td>
        <td>
            <b class="tx">UF:</b> {{$agendamento->Cliente->Estado->nome}}
        </td>
    </tr>
    <tr>
        <td>
            <b class="tx">Rg:</b> {{$agendamento->Cliente->rg}}
        </td>
        <td>
            <b class="tx">Org Exp:</b> {{$agendamento->Cliente->exp_rg}}
        </td>

    </tr>
    <tr>
        <td>
            <b class="tx">Telefones:</b>  {{$tel_1.' - '.$tel_2}}
        </td>
        <td>
            <b class="tx">CPF/CNPJ:</b> {{$cpfCnpj}}
        </td>
    </tr>

</table>
</table>
<div class="retangulo">
    <span class="texto">VEÍCULO</span>
</div>
<table class="tabelas" id='ficha'>
    <tr>
        <td>
            <b class="tx">Marca:</b> {{$agendamento->Veiculo->Marca->nome}}
        </td>
        <td>
            <b class="tx">Modelo:</b> {{$agendamento->Veiculo->modelo}}
        </td>
    </tr>
</table>
<div class="retangulo">
    <span class="texto">LOCAÇÃO</span>
</div>
<table class="tabelas" id='ficha'>
    <tr>
        <td>
            <b class="tx">Data da Saída:</b> {{\Carbon\Carbon::parse($agendamento->data_saida)->format('d/m/Y')}}
        </td>
        <td>
            <b class="tx">Hora da Saída:</b> {{$agendamento->hora_saida}}
        </td>
        <td>
            <b class="tx">Data do Retorno:</b> {{\Carbon\Carbon::parse($agendamento->data_retorno)->format('d/m/Y')}}
        </td>
        <td>
            <b class="tx">Hora do Retorno:</b> {{$agendamento->hora_retorno}}
        </td>
    </tr>
    <tr>
        <td>
            <b class="tx">Qtd de Diárias:</b> {{$agendamento->qtd_diarias}}
        </td>
        <td>
            <b class="tx">Valor Total R$:</b> {{$agendamento->valor_total}}
        </td>

        <td>
            <b class="tx">Desconto R$:</b> {{$agendamento->valor_desconto}}
        </td>
        <td>
            <b class="tx">Valor Pago R$:</b> {{$agendamento->valor_pago}}
        </td>
    <tr>
        <td colspan="4">
            <b class="tx">Valor Restante R$:</b> {{$agendamento->valor_restante}}
        </td>
    </tr>
    </tr>
</p>
</table>

<table class="tabelas" id='ficha'>
    <tr>
        <td>
            <b class="tx">Observações: </b> {{$agendamento->obs}}
        </td>
    </tr>
</table>


    <div class="tela">

            Lajedo, {{ $dataAtual->isoFormat('DD MMMM YYYY')}}<br><br>
            _______________________________________<br>
                         Locador<br><Br>
            _________________________________________<br>
                         Locatário
    </div>
</body>
</html>
