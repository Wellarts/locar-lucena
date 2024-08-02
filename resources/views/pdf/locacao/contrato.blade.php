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
        font-size: 16px;

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
        font-size: 15px;
    }

</style>

<style>
    .tela {
        width: 100%;
        height: 100px;
        border: 0px solid black;
        float: center;
        text-align: center;

    }
</style>

</head>
<body>

<table style="width: 100%">
  <tr>
    <td><img src="{{ asset('img/logo-lucena.png') }}" alt="Image" height="60" width="180"></td>
    <td> <p style="width: 100%; font-size:28px; font-weight: bold;" align="center">Locadora Lucena</p>
         <p style="font-size:16px;" align="center">Av. Cesário de Melo, nº 4030 Campo Grande - Rio de Janeiro - RJ.<br>
                                                  Contato: (21)7402-1183<br>
                                                  Email: erike@rdbled.com.br - CNPJ: 53-825-708/0001-48</p>
    </td>
</tr>

</table>
<div class="retangulo">
    <span class="texto">FICHA DE LOCAÇÃO</span>
</div>
<table>
</table>
<div class="retangulo">
<span class="texto">LOCATÁRIO</span>
</div>

<table class="tabelas" width="100%" id='ficha'>
<tr>
    <td colspan="2">
        <b class="tx">Nome:</b> {{$locacao->Cliente->nome}}</p>
    </td>
</tr>
<tr>
    <td colspan="2">
        <b class="tx">Endereço:</b>  {{$locacao->Cliente->endereco}}
    </td>
<tr>
    <td>
        <b class="tx">Cidade:</b> {{$locacao->Cliente->Cidade->nome}}
    </td>
    <td>
        <b class="tx">UF:</b> {{$locacao->Cliente->Estado->nome}}
    </td>
</tr>
<tr>
    <td>
        <b class="tx">Rg:</b> {{$locacao->Cliente->rg}}
    </td>
    <td>
        <b class="tx">Org Exp:</b> {{$locacao->Cliente->exp_rg}}
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
        <b class="tx">Marca:</b> {{$locacao->Veiculo->Marca->nome}}
    </td>
    <td>
        <b class="tx">Modelo:</b> {{$locacao->Veiculo->modelo}}
    </td>
    <td>
        <b class="tx">Chassi:</b> {{$locacao->Veiculo->chassi}}
    </td>
</tr>
<tr>
    <td>
        <b class="tx">Ano:</b>  {{$locacao->Veiculo->ano}}
    </td>
    <td>
        <b class="tx">Cor:</b>  {{$locacao->Veiculo->cor}}
    </td>
    <td>
        <b class="tx">Placa:</b>  {{$locacao->Veiculo->placa}}
    </td>
</tr>
</table>
<div class="retangulo">
<span class="texto">LOCAÇÃO</span>
</div>
<table class="tabelas" id='ficha'>
<tr>
    <td>
        <b class="tx">Data da Saída:</b> {{\Carbon\Carbon::parse($locacao->data_saida)->format('d/m/Y')}}
    </td>
    <td>
        <b class="tx">Hora da Saída:</b> {{$locacao->hora_saida}}
    </td>

    <td>
        <b class="tx">Data do Retorno:</b> {{\Carbon\Carbon::parse($locacao->data_retorno)->format('d/m/Y')}}
    </td>
    <td>
        <b class="tx">Hora do Retorno:</b> {{$locacao->hora_retorno}}
    </td>
</tr>
    <td>
        <b class="tx">Km de Saída:</b>  {{$locacao->km_saida}}
    </td>
    <td>
        <b class="tx">Qtd de Diárias:</b> {{$locacao->qtd_diarias}}
    </td>
    <td colspan="2">
        <b class="tx">Valor da Diária R$:</b> {{$locacao->Veiculo->valor_diaria}}
    </td>

</tr>
<tr>
    <td >
        <b class="tx">Km de Retorno:</b> {{$locacao->km_retorno}}
    </td>
    <td >
        <b class="tx">Valor do Desconto R$:</b> {{$locacao->valor_desconto}}
    </td>
    <td colspan="2">
        <b class="tx">Valor Total R$:</b> {{$locacao->valor_total_desconto}}
    </td>

</tr>

</table>

<table class="tabelas" id='ficha'>
<tr>
    <td>
        <b class="tx">Observações: </b> {{$locacao->obs}}
    </td>
</tr>
</table>

<!-- PÁGINA 2 -->

<style>
    .break {
        page-break-before: always;
         }
    .parag {
        text-align: justify;
        font-size: 11;
    }
</style>

<div class="break">

<table style="width: 100%">
    <tr>
        <td><img src="{{ asset('img/logo-lucena.png') }}" alt="Image" height="60" width="180"></td>
      <td> <p style="width: 100%; font-size:20px; font-weight: bold" align="center">Contrato de Locação de Veículos</p>

      </td>
  </tr>
  </table>
</div>
<div>
    <p class="parag">
        Registro: <b>{{$locacao->Veiculo->id}}</b><br><br>
        Parte, locadora. Lucena Campo Grande Ltda CNPJ 53.825.708/0001-48 Endereço: AV. Cesario de Melo 04030 denominado LOCADOR.<br><br>


        CLÁUSULA 1 DO OBJETO DO CONTRATO:<br>
        1.1- Por meio deste contrato regula-se a locação da motocicleta da marca: <b>{{$locacao->Veiculo->modelo}} - {{$locacao->Veiculo->cor}} - {{$locacao->Veiculo->ano}}.</b>.<br>

        1.2. O veículo descrito acima, será utilizado exclusivamente pelo LOCATÁRIO, não sendo permitido sub-rogar para terceiros os direitos por ele obtidos
        contrato do presente, nem permitir que outra pessoa conduza o referido veículo sem a inequívoca e expressa autorização do LOCADOR, sob pena de
        rescisão contratual, multa de R$ 600,00 (seiscentos reais), bem como, a responsabilização total por qualquer ato ou danos em relação ao veículo,
        inclusive os provenientes de caso fortuito ou força maior.<br>
        CLÁUSULA 2ª – DO HORÁRIO DOALUGUÉL E LOCAL DE COLETA E DEVOLUÇÃO DO VEÍCULO.<br>
        2.1. O veículo em questão permanecerá na posse do LOCATÁRIO por período integral, de segunda a domingo.<br>
        2.2.O LOCATÁRIO deverá apresentar o veículo ao LOCADOR 01 (uma) vez por mês para a realização de vistoria, em data e endereço por este designado.<br>
        2.3.A não apresentação do veículo, no prazo e os supracitados locais, serão cobrados ao LOCATÁRIO multa de R$ 150,00 (cento e cinquenta reais)<br>
        mais R$ 50,00 (cinquenta reais) por dia de atraso, além da possível rescisão contratual.<br>
        CLÁUSULA 3ª – DAS OBRIGAÇÕES DO LOCADOR<br>
        3.1. O veículo objeto do presente contrato será submetido à manutenção preventiva periódica, ou em decorrência de problemas mecânicos e/ou<br>
        elétricos, aos quais o LOCATÁRIO não deu causa, em oficina mecânica designada pelo LOCADOR, nos termos a seguir:<br>
        3.1.1. Troca do Kit de Tração: Sempre que houver barulho anormal e/ou apresentar desgaste excessivo;<br>
        3.1.2. Troca de Pneus: Quando estiverem no nível do Tread Wear Indicator (TWI);<br>
        3.1.3. Se o LOCATARIO não fazer a devolução da moto ao término do contrato, na data e horário estipulados, o LOCADOR fica no direito de renovar
        automaticamente o contrato pelo mesmo tempo da locação anterior.<br>
        3.2. Caso, em alguma das manutenções supracitadas, seja necessária antes ou durante o período estipulado, deverá ser arcada integralmente pelo
        LOCADOR, salvo nos casos em que o LOCATÁRIO tenha dado causa ao evento, por mau uso.<br>
        3.3. Os gastos decorrentes da manutenção preventiva periódica supracitada, bem como o valor pago pela mão de obra do profissional que realizará o
        serviço, serão suportados pelo LOCADOR.<br>
        3.4. As manutenções que não foram citadas na cláusula<br>
        3.1 também serão arcadas pelo LOCADOR, quando forem necessárias e atestadas pelo mecânico do mesmo.<br>
        3.5. No caso de problemas mecânicos e/ou elétricos (quebra, defeito e/ou desgaste) percebidos em ocasião diversa da manutenção preventiva
        periódica, o LOCATÁRIO deverá informar imediatamente ao LOCADOR, bem como apresentar o veículo a este, no prazo de 24 horas, para reparo a
        ser realizado em oficina mecânica designada pelo LOCADOR.<br>
        3.6. O LOCADOR manterá proteção veicular no veículo, caso o LOCATÁRIO contrate os planos de seguro descrito no início deste contrato, caso não
        haja interesse de adquirir proteção veicular por parte do LOCATÁRIO, o veículo seguirá por total responsabilidade civil e criminal do LOCATÁRIO,
        ficando responsável por quaisquer danos causados ao veículo alugado ou a terceiros.<br>
        3.6.1 Caso o LOCATÁRIO opte por contratar alguma proteção veicular (seguro para pane, seguro para terceiros, seguro para o veículo alugado e
        seguro com moto reserva), fica a observação em que o seguro veicular só será válido dentro do perímetro de até 230 km (duzentos e trinta quilómetros)
        de distância da base da locadora, sendo que, a proteção veicular para terceiros fixada no valor máximo a ser coberto em até R$ 10.000,00 (dez
        mil reais), caso os danos a terceiros ultrapassar trinta mil reais, o valor excedente deve ser cobrado do LOCATÁRIO.
        3.7. O LOCADOR não é obrigado a disponibilizar veículo reserva (caso o mesmo não seja contratado previamente) e não se responsabiliza caso o
        LOCATÁRIO não possa dirigir devido à indisponibilidade do veículo.<br>
        CLÁUSULA 4ª – DAS OBRIGAÇÕES DO LOCATÁRIO<br>
        4.1. É de responsabilidade do LOCATÁRIO, a observância básica dos itens do veículo, como calibragem dos pneus, nível de óleo do motor, nível de
        fluido de freio, observância da marcação, sistema de iluminação e sinalização, entre outros.<br>
        4.1.1. Quaisquer danos/avarias ao veículo serão apurados no final do contrato e os custos de reparação serão arcados pelo LOCATÁRIO.<br>
        4.1.2. Os custos de revisões reparatórias causadas pelo mau uso dos veículos correrão por conta do LOCATÁRIO. Caso a bomba de combustível
        queime ou danifique por falta de combustível ou negligência, quando o veículo estiver em posse do LOCATÁRIO, este deverá arcar com o valor integral
        da peça, mão de obra, reboque do veículo e demais valores inerentes ao reparo.<br>
        4.2. É de responsabilidade do LOCATÁRIO o pagamento de quaisquer multas relativas às infrações de trânsito inerentes à utilização do veículo
        cometidas na vigência deste contrato.<br>
        4.2.1. O pagamento das multas pelo LOCATÁRIO deverá ser realizado imediatamente após a constatação no sistema do DETRAN,
        independentemente de qualquer procedimento, seja transferência de pontos ou recurso.<br>
        4.2.2. O LOCATÁRIO concorda que o LOCADOR irá indicá-lo como condutor/infrator responsável pelas infrações de trânsito apuradas durante a
        locação, nos termos do artigo 257, parágrafos 1º, 3º, 7º e 8º do Código de Trânsito. A partir da indicação, o LOCATÁRIO terá legitimidade para se
        defender perante o órgão atuador.<br>
        4.2.3. Qualquer questionamento sobre eventual improcedência de infração de trânsito deverá ser feito exclusivamente pelo LOCATÁRIO perante o
        órgão autuado.<br>
        4.3. Ao ocorrer as multas acima mencionadas, quando a autuação da infração chegar ao LOCADOR, deverá o LOCATÁRIO comparecer em local e
        data estipulados pelo LOCADOR, para a assinatura do auto de infração, com o intuito de transferência dos pontos para a sua CNH, sob pena de
        pagamento ao LOCADOR na quantia de R$ 200,00 (duzentos reais), em caso de perda do prazo para a transferência dos pontos.<br>
        4.4. Caso o veículo seja rebocado por estacionamento irregular, ou outra hipótese a qual tenha dado causa, o LOCATÁRIO deverá arcar com todos os
        custos necessários para a recuperação do veículo junto ao respectivo depósito público. O LOCATÁRIO deverá arcar também com multa contratual de
        R$ 30,00 (trinta reais) por dia pelo período em que a moto estiver no depósito, a título de lucro cessante.<br>
        4.5. É proibido o LOCATÁRIO acionar o serviço de proteção veicular do veículo, objeto deste contrato, sem a expressa permissão do LOCADOR, sob
        pena de multa de R$ 200,00 (duzentos reais), além da obrigação de arcar com eventuais custos de reboques e/ou transportes necessários, caso o
        serviço de proteção veicular não os disponibilize mais, devido ao limite de utilizações mensais deste serviço.<br>
        4.6.O LOCATÁRIO se responsabiliza por quaisquer acessórios do veículo que estiverem em sua posse, como por exemplo chave de ignição,
        documento do veículo, entre outros. Caso algum acessório do veículo seja perdido ou danificado, o LOCATÁRIO deverá arcar com todos os custos
        necessários à reposição.<br>
        4.7. É proibido o LOCATÁRIO sair do perímetro urbano da cidade onde a moto foi alugada, com o veículo objeto deste contrato, sem a autorização
        expressa e por escrito do LOCADOR, sob pena de multa de R$ 150,00 (cento e cinquenta reais), exceto se o LOCATÁRIO adquiriu o plano saída da
        cidade, além do pagamento dos custos para o retorno do veículo para base da locadora, bem como o pagamento de eventuais danos ocorridos com o
        veículo, inclusive caso furto e força maior. 4.9. Em caso de roubo ou furto do veículo, o LOCATÁRIO se compromete em avisar imediatamente ao
        LOCADOR, bem como a comparecer à delegacia de polícia mais próxima da residência do LOCADOR para registrar a ocorrência.<br>
        4.8. O LOCATÁRIO se compromete a comparecer à sede da empresa de proteção veicular, ou outro local especificado por ela, a fim de cumprir com
        procedimento de indenização do veículo.<br>
        4.9. Caso o LOCATÁRIO se envolva em sinistro, estando sob efeito de álcool/entorpecentes, ou se não fizer o teste de embriaguez requerido pela
        autoridade, este deverá pagar ao LOCADOR o valor da tabela FIPE do veículo, caso a indenização da proteção veicular seja negada e/ou com todos
        os custos inerentes à recuperação do veículo junto ao depósito, em caso de reboque.<br>
        4.10. O LOCATÁRIO deve manter as características originais do veículo, deste modo, a instalação de adesivos, pinturas especiais,
        equipamentos e outros, na constatação da falta ou danos e descaracterização do veículo, fica sujeito a multa de R$ 150,00 mais o pagamento
        do reparo ou substituição de peças ou avarias do veículo.<br>
        4.11. É de responsabilidade do LOCATÁRIO o pagamento e a troca do óleo do motor a cada 1.000 km (mil quilômetros) rodados e filtro, de
        acordo com as especificações do fabricante do veículo. Será exigido foto do painel do veículo e nota fiscal da compra do óleo, em caso da
        não realização da troca de óleo no período certo, fica sujeito a multa de R$ 100,00 (cem reais).<br>
        4.12. Aceitar que o LOCADOR promova, pelos meios processuais de que venha a dispor, o seu chamamento aos feitos judiciais promovidos por
        terceiros decorrentes de eventos com o veículo alugado, cabendo-lhe assumir o polo passivo nas demandas, inclusive quanto aos valores reclamados
        por terceiros e/ou para assegurar os direitos regressivos do LOCADOR. O LOCATÁRIO será responsável pelo pagamento de lucros cessantes que
        terceiros possam pleitear judicialmente em razão de conduta irregular do LOCATÁRIO.<br>
        CLÁUSULA 5ª – DAS OBRIGAÇÕES DECORRENTES DE COLISÕES E AVARIAS DO VEÍCULO<br>
        5.1. É de responsabilidade do LOCATÁRIO o pagamento do reboque, taxas e reparos ao veículo objeto do presente contrato ou a veículo de outrem,
        na ocorrência de acidentes e colisões sofridas na vigência do presente contrato, quando não contempladas pela cobertura da proteção veicular
        contratada para este veículo, independente de dolo, culpa, negligência, imprudência ou imperícia do LOCATÁRIO.<br>
        5.2. Na ocorrência da necessidade do pagamento da cota de participação da proteção veicular, a quantia será integralmente de
        responsabilidade do LOCATÁRIO, no valor de R$ 900,00 (novecentos reais), 24 (vinte e quatro) horas a contar da comunicação ao LOCADOR.
        No caso em que o LOCATÁRIO resilir o presente contrato, 24 (vinte e quatro) horas a contar do momento em que teve ciência da resilição,
        quando realizada pelo LOCADOR.<br>
        5.3. Será de responsabilidade do LOCATÁRIO, o pagamento de taxas e diárias para a liberação do veículo decorrentes ao reboque realizado pelo
        Poder Público, nos casos supracitados.<br>
        5.4.A responsabilidade determinada nos itens supracitados permanece estabelecida, inclusive, caso o LOCATÁRIO não se encontre no interior do
        veículo objeto do presente contrato.<br>
        CLÁUSULA 6ª – DO PAGAMENTO EM RAZÃO DA LOCAÇÃO DO VEÍCULO<br>
        6.1.O LOCATÁRIO pagará ao LOCADOR o valor citado no início do contrato semanalmente, realizado em toda segunda-feira.<br>
        6.2. Caso o pagamento seja realizado após a data acordada, o valor sofrerá um acréscimo de R$ 30,00 (trinta reais) a título de multa, bem
        como um acréscimo de R$ 20,00 (vinte reais) por dia de atraso a título de juros.<br>
        6.3. Após 24 (vinte e quatro) horas do não pagamento semanal acordado, o LOCADOR poderá encerrar o contrato e bloquear o veículo, entendendo
        que o não pagamento confirma o cancelamento do contrato por parte do LOCATÁRIO, e que o LOCADOR poderá usar o valor caução para cobrir os
        débitos e restante das diárias prevista em contrato da cláusula 8.1 e 8.1.1.<br>
        CLÁUSULA 7ª – DA QUANTIA CAUÇÃO<br>
        7.1. Estabelecem como partes, a QUANTIA CAUÇÃO no valor total descrito no início do contrato, a ser integralizada até o ato de retirada do veículo.
        7.2. Ao término da vigência do presente contrato caberá ao LOCADOR restituir a integralidade da QUANTIA CAUÇÃO ao LOCATÁRIO no prazo de 30
        (trinta) dias úteis, a contar da devolução do veículo por parte do LOCATÁRIO, conforme as seguintes CONDIÇÕES: A devolução do veículo em perfeito
        estado, em condição equivalente à observada ao último checklist de vistoria, e após vistoria realizada por vídeo enviado para o WhatsApp do
        LOCADOR; a inexistência de aluguéis, multas de trânsito ou multas por descumprimento contratual pendente por parte do LOCATÁRIO; após
        realização da manutenção necessária do veículo, caso haja necessidade; após descontados quaisquer outros subsídios pendentes.<br>
        7.3. Na hipótese de não estarem observadas as condições acima dispostas, o LOCADOR poderá utilizar-se da QUANTIA CAUÇÃO para adimplir
        subsídios eventuais ou reparar danos causados ao veículo, que não decorram o desgaste natural e a utilização adequada do bem, hipóteses em que
        só será de direito do LOCATÁRIO a quantidade remanescente a tal utilização da QUANTIA CAUÇÃO.<br>
        7.4. Os gastos com o combustível do veículo deverão ser arcados integralmente pelo LOCATÁRIO, e sempre devolver o veículo com a mesma
        quantidade de combustível contido no veículo quando a entrega deste foi realizado pelo LOCADOR, sob pena de desconto na QUANTIA CAUÇÃO do
        valor necessário a atingir tal quantidade de combustível.<br>
        7.5. Qualquer valor que implica a cobrança por passagem, estacionamento ou pedágio do veículo durante a posse do LOCATÁRIO, deverá por este ser
        arcado. Caso o LOCADOR seja cobrado por qualquer valor desta natureza, o LOCATÁRIO deverá reembolsá-lo imediatamente.<br>
        7.6. Caso o veículo seja devolvido sujo, será cobrada higienização no valor R$ 80,00 (oitenta reais). Na hipótese de lavagem especial será
        cobrada também 1 (um) dia de locação, ou quanto for permitido, até a disponibilização do veículo, limitadas a 10 (dez) diárias.<br>
        7.7. Quando o documento do veículo não for devolvido, será cobrada uma taxa de R$ 150,00 (cento e cinquenta reais).
        CLÁUSULA 8ª – DA VIGÊNCIA E RESCISÃO<br>
        8.1.O presente contrato se inicia na data de sua assinatura com prazo mencionado no início do contrato. A motocicleta
        mencionada deverá ser devolvida no final do contrato até às 12h00 (doze horas) ao LOCATÁRIO, caso não seja devolvida no dia previsto, o contrato
        poderá ser renovado automaticamente.<br>
        8.1. Em caso de devolução antecipada, o LOCATÁRIO pagará uma multa no valor de 50% (cinquenta por cento) das diárias restantes, não
        recuperando qualquer ônus ao LOCADOR.<br>
        8.2. É assegurado às partes a resiliência do presente CONTRATO a qualquer tempo, bastando, para tanto, dar ciência a outra parte, cabendo ao
        LOCATÁRIO a devolução do veículo ao LOCADOR em local designado por este no seguinte prazo: 24 (vinte e quatro) horas a contar da comunicação
        ao LOCADOR. No caso em que o LOCATÁRIO resista ao presente contrato. 24 (vinte e quatro) horas a contar do momento em que teve ciência da
        resiliência, quando realizada pelo LOCADOR.<br>
        8.2.1. Caso seja necessário o acionamento de guincho/reboque para buscar o veiculo alugado, será cobrado toda a despesa de transporte do locatário.
        8.3.O contrato poderá ser rescindido em pleno direito pelo LOCADOR, independentemente de qualquer notificação, e este, sem mais formalidades,
        providenciará a retomada do veículo, sem que isso enseje ao LOCATÁRIO qualquer direito de retenção, indenização ou devolução da quantia caução,
        quando:<br>
        8.3.1. O veículo não pode ser devolvido na data, hora e local previamente ajustados;<br>
        8.3.2. Corrija o uso inadequado do veículo;<br>
        8.3.3. Corrigir apreensão do veículo localizado por autoridades competentes;<br>
        8.3.4. O LOCATÁRIO não quitar seus subsídios nos respectivos vencimentos;<br>
        8.3.5. O LOCATÁRIO acumular uma dívida superior a R$ 50,00 (cinquenta reais), caso no qual o veículo deverá ser entregue no local determinado pelo
        LOCADOR, imediatamente, sob pena de multa de R$ 150,00 (cento e cinquenta reais) por dia, salvo acordo contrário entre as partes.<br>
        8.4. Fica desde já, pactuada a total inexistência de vínculo trabalhista entre as partes do presente contrato, sendo indevida toda e qualquer incidência
        das obrigações previdenciárias e dos encargos sociais, não havendo entre as partes, qualquer tipo de subordinação e controle típicos de relações de
        emprego.<br>
        8.5. Nos termos do artigo 265 do Código Civil Brasileiro, inexiste solidariedade, seja contratual ou legal entre o LOCADOR e o LOCATÁRIO, razão pela
        qual, com a locação e a efetiva retirada do veículo motorizado, o LOCATÁRIO assume sua posse autônoma para todos os fins de direito,
        responsabilizando-se por eventuais indenizações decorrentes do uso e circulação do veículo, cuja responsabilidade perdurará até a efetivação da
        devolução do veículo alternativo.<br>
        CLÁUSULA 9ª – DA DEVOLUÇÃO DO VEÍCULO<br>
        9.1. Ao término do contrato, o veículo deverá ser devolvido no local, dia e hora indicados pelo LOCADOR, sob pena de multa de R$ 150,00 (cento e
        cinquenta reais) por dia.<br>
        9.2.A não devolução de veículo pelo LOCATÁRIO, após notificação realizada pelo LOCADOR, configura crime de APROPRIAÇÃO INDÉBITA
        conforme artigo 168 do Código Penal Brasileiro, com pena de reclusão de um a quatro anos de prisão e multa.<br>
        CLÁUSULA 10ª - DAS DISPOSIÇÕES GERAIS<br>
        10.1. Quaisquer notificações e comunicados enviados sob esse contrato podem ser realizados de forma eletrônica (e-mail ou WhatsApp), escritos ou
        por correspondência com aviso de recepção aos endereços constantes do preâmbulo. Ficam as partes obrigadas a fornecer tal informação.<br>
        10.2. Todos os valores, despesas e encargos de locação específicos de dívidas líquidas e específicos para pagamento à vista, passíveis de cobrança
        executiva.<br>
        10.3. Eventuais tolerâncias do LOCADOR para com o LOCATÁRIO no cumprimento das obrigações ajustadas neste contrato específico mera
        liberalidade, não importando em hipóteses de novação ou renúncia, permanecendo integralmente as cláusulas e condições aqui contratadas.<br>
        10.4. O LOCATÁRIO autoriza o LOCADOR a coletar, usar e divulgar sua imagem para fins de cadastro, defesa e/ou promoção.<br>
        10.5. O LOCATÁRIO concorda que sua assinatura no contrato, implica ciência e adesão por si, seus herdeiros/sucessores a estas cláusulas.<br>
        10.6. Fica eleito o Foro desta cidade e Comarca de Rio de Janeiro RJ, como competente para dirimir quaisquer questões que possam
        aconselhar da aplicação do presente CONTRATO, por mais privilegiado que seja ou venha a ser, qualquer Foro.<br>
        10.7. E, por serem assim, justas e contratadas, as partes firmam o presente instrumento em 02 (duas) vias de igual teor e forma, para que produza
        seus efeitos legais, após ter lido o seu conteúdo ter sido claramente entendido e aceito.<br>



</div><br><br>

        <div style="text-align: center; font-size: 12">Rio de Janeiro, {{ $dataAtual->isoFormat('DD MMMM YYYY') }}<br><br><br><br>

            ___________________________________________________________<br>
            LOCATÁRIO: {{$locacao->Cliente->nome}}<br><Br><br><br>

            ___________________________________________________________<br>
            LOCADOR: MOTOMASTER CAMPO GRANDE LTDA.



        </div>











</body>
</html>
