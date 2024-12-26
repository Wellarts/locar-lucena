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
    padding: 0px 2px;
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
         <p style="font-size:16px;" align="center">Av. Agamenon Magalhães, nº 160c Centro - Lajedo - PE.<br>
                                                  Contato: (87)9-9608-0251 (87)9-9602-9051<br>
                                                  Email: fllocacaodeveiculos@gmail.com - CNPJ: 23-413-119/0001-91</p>
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


<div class="container">
    <div class="tela">
        <table>
            <tr>
                <td>
                    <h3>Inspeção de Veículo</h3>
                    <tr>
                        <td>( ) Documento</td>
                    </tr>
                    <tr>
                        <td>( ) Calota</td>
                    </tr>
                    <tr>
                        <td>( ) Macaco</td>
                    </tr>
                    <tr>
                        <td>( ) Estepe</td>
                    </tr>
                    <tr>
                        <td>( ) Trava</td>
                    </tr>
                    <tr>
                        <td>( ) Triângulo</td>
                    </tr>
                    <tr>
                        <td>( ) Tapetes</td>
                    </tr>
                    <tr>
                        <td>( ) Ar Condicionado</td>
                    </tr>
                    <tr>
                        <td>( ) Radio CD</td>
                    </tr>
                    <tr>
                        <td>( ) Retrovisores</td>
                    </tr>
                    <tr>
                        <td>( ) Antena</td>
                    </tr>
                    <tr>
                        <td>( ) Pen Driver</td>
                    </tr>
                </td>
            </tr>
        </table>

    </div>
</div>
<div style="margin-left: 300px">
    <table style="width: 100%">
        <tr>
            <td><p style="margin-left: 90px">Lajedo, {{ $dataAtual->isoFormat('DD MMMM YYYY') }}</p></td>
        </tr>
        <br><br><br>
        <tr>
            <td>
                <tr>
                    <td><center>_________________________________________</center><br>
                                    <center>Locador</center>
                    <p style = "font-size:10px; text-align: center">Proprietário ou representante legal da Empresa</p>
                    </td>
                </tr>

            </td>
        </tr>

        <tr>
            <td>
                <tr>
                    <td>
                        <div>
                           <center> _________________________________________</center><br>
                                    <center>Locatário</center>
                                    <p style = "font-size:10px; text-align: justify">Pelo Presente termo, o LOATÁRIO autoriza a LOCADORA a
                                    encaminhar ao Depatarmento de Trânsito, em nome do(s) motorista(s) que utilizar(em)
                                    o(s) veículo acima, a(s) decorrente(s) de infrações eventualmente cometidas,
                                    no período de locação. </p>
                        </div>
                    </td>
                </tr>

            </td>
        </tr>
       </table>

</div>


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
        1. LOCADORA DE VEÍCULOS LUCENA- EIRELI-ME  , inscrita no CNPJ nº 23.413.119/0001-91, com endereço na Avenida
        Agamenon Magalhães, nº 160C, Centro de Lajedo/PE, doravante denominada LOCADORA, e se regerá pelas cláusulas e
        condições seguintes:<br>
        <b>LOCATÁRIO:</b><br>
        <b>Nome:</b>  {{$locacao->Cliente->nome}}<br>
        <b>Endereço:</b> {{$locacao->Cliente->endereco}}<br>
        <b>Cidade:</b> {{$locacao->Cliente->Cidade->nome}} - {{$locacao->Cliente->Estado->nome}}<br>
        <b>CPF/CNPJ:</b> {{$cpfCnpj}} <b>RG:</b> {{$locacao->Cliente->rg}} <b>EXP:</b> {{$locacao->Cliente->exp_rg}}<br><br>
        As partes acima têm entre si justo e contratado a locação de veículo, descrito no ANEXO 1 do presente contrato, nos seguintes
        temos e condições:<br>
        1.1. O veículo alugado, temporariamente, é de propriedade da empresa LOCADORA, encontrando-se em perfeitas condições
        mecânicas de uso, conservação e funcionamento, tendo sido revisado antes de ser posto à disposição do cliente e assim deve ser
        devolvido ao término do contrato de aluguel.<br><br>
        1.2. Os dados do veículo alugado e demais características da locação serão anotadas no ANEXO 1 desse contrato, denominado
        "Demonstrativo de Aluguel de Veiculos", o qual deverá ser assinado pelo cliente, tornando, desta maneira, os dados e valores ali
        lançados, líquidos, certos e exigíveis.<br><br>
        1.3. Para os fins de comprovação das condições do veículo alugado (cláusula 1) será realizado, tanto no ato da entrega do veiculo
        ao cliente, quanto na devolução, um “check list, conforme o ANEXO 2 deste contrato, assinado pelo cliente (cláusula 1.4), valendo
        este documento como comprovante da data e condições/estado em que fora entregue e posteriormente devolvido o veiculo,
        podendo a LOCADORA valer-se do mesmo para cobrança judicial ou debito ejou reparação de danos ou falta de acessórios do
        veiculo alugado.<br><br>
        1.4.4 devolução do veiculo deverá ser efetuada pelo cliente. Todavia, caso esteja impossibilitado de fazê-lo, poderá ser realizada
        por terceira pessoa, ficando, pela presente cláusula, convencionado que o cliente outorgou ao terceiro plenos poderes para o ato,
        inclusive valendo sua assinatura no encerramento do contrato (cláusula 1.2) e no “check list" (cláusula 1.3) para os fins de direito,
        tornando os dadosivalores do ANEXO 1 e 2, líquidos, certos e exigíveis.
        1.5. Ao assinar o ANEXO 1, bem como o ANEXO 2, o cliente reconhece como verdadeiros os dados ali inseridos para todos os
        efeitos legais.<br><br>
        <b>DA PROTEÇÃO LOCADORA</b><br><br>
        2.1. A LOCADORA tem por dever realizar os serviços de manutenção mecânica do veículo alugado, antes do ato da entrega
        deste, nos termos deste contrato.<br><br>
        2.2. O valor da locação do veiculo pretendido é o preço básico, sem qualquer proteção oferecida pela LOCADORA, assumindo o
        cliente todos os possíveis danos materiais e pessoais - inclusive contra terceiros - que vierem a ocorrer com relação ao veículo
        alugado.<br>
        2.3.A LOCADORA, entretanto, oferece proteção em casos de furto, roubo, acidentes.<br><br>
        2.34. Proteção do carro alugado: a proteção parcial oferecida pela LOCADORA cobre os danos materiais que por ventura
        ocorrerem no veículo alugado, como furto, roubo, incêndio e acidente deste, tendo o cliente de participar com o pagamento de
        franquia.<br><br>
        2.4.4 proteção LOCADORA não se entende a equipamentos de som (íradio, toca fitas, CD players, etc), bem como a acessórios
        dos carros alugados.<br><br>
        <b>DAS OBRIGAÇÕES DO CLIENTE</b><br><br>
        O cliente se compromete a:<br>
        3.1. Devolver o veículo na sede da LOCADORA, na data prevista no Demonstrativo de Aluguel de Veículos (ANEXO 1), sob pena
        de configuração de apropriação indébita (art. 188, do Código Penal), com sujeição às penas da lei, inclusive busca e apreensão do
        mesmo.<br><br>
        3.2. Responsabilizar-se pela quarda e correto uso do veiculo, trafegando unicamente em rodovias efou ruas de tráfego regular,
        dentro das normas do Código Nacional de Transito.
        3.3. Usar o veículo sem fins lucrativos e apenas para transporte de pessoas, observando seu limite de capacidade, sendo
        conduzido apenas pelo cliente ou motorista indicado no Demonstrativo do Contrato de Aluguel (ANEXO 1), sob pena de infração
        contratual e perda das garantias LOCADORA.<br><br>
        3.4. Usar o veículo exclusivamente dentro do território nacional.<br><br>
        3.5. Comunicar à LOCADORA imediatamente ocorrência de acidente, furto, roubo ou incêndio e providenciar Boletim de
        Ocorrência Policial ou Laudo Pericial, quando este se fizer necessário, no prazo máximo de 2 (dois) dias após o evento, sob pena
        de perda das garantias LOCADORA optada na contratação do aluguel, além de responsabilização pelas consequências do
        ocorrido.<br><br>
        3.6. Quando da ocorrência de avaria no veiculo, deve de imediato entrar em contato com a locadora ou assistência por ela
        indicada no momento da locação do veiculo, recebendo instruções de como proceder para solucionar o problema ocorrido através
        de oficina indicada.<br><br>
        3.7. Pagar o total do aluguel por ocasião da devolução do veiculo ou, em caso de busca e apreensão, até a efetiva entrega do
        mesmo, bem como o valor do combustível faltante para completar o tanque.<br><br>
        3.8. Pata os efeitos do Parágrafo 7o, artigo 257 do Código Nacional de Trânsito, entregar à LOCADORA, no ato da assinatura
        deste contrato, o nome e endereço do condutor infrator, bem como xerox de sua CNH, CIC e RG, pena de responsabilizar-se
        plenamente pela nova multa que será lavrada nos termos do Parágrafo 8º do Artigo referido. Caso não informe à locadora os
        dados do condutor infrator, fornecerá ela ao DETRAN todos os dados do cliente ou do usuário, caso aquele não seja habilitado,
        necessários para a cobrança da multa ou outras penalidades, para tanto lançando mão do mandato referido no Anexo 1.<br><br>
        3.9. Reembolsar a locadora de eventuais despesas efetuadas para reparação de danos decorrentes do mau uso do veiculo, bem
        como outras despesas afins, como guinchamento do veiculo locado ejou terceiros prejudicados.<br><br>
        310. O não atendimento dos disposto nas cláusulas 3.11, 3.12 e 3.13 configurará inadimplência contratual, ensejando a emissão
        de Nota Fiscal e Duplicata de Prestação de Serviços, pela LOCADORA, acrescido dos valores apurados a titulo de despesas
        administrativas, bem como multa contratual na base de 2% (dois por cento), além de juros de 1% (um por cento) ao mês e
        correção monetária, ficando a locadora por este instrumento autorizada a protestar a citada Duplicata, como título de divida liquida
        e certa.<br><br>
        3.11. A infração de qualquer dos itens do presente capitulo (Das Obrigações do Cliente) implicará na perda da garantia
        LOCADORA optada.<br><br>
        <b>DO USO INDEVIDO DO VEICULO</b><br><br>
        4. Configurar-se-á o uso indevido do veiculo e infração contratual, com perda da proteção LOCADORA, quando:<br>
        a) em caso de acidente, furto, roubo ou colisão, tiver procedido com manifesto dolo ou culpa (imprudência, imperícia ou
        negligencia), eiou utilizado o veiculo para fins diversos da destinação especifica constante no Certificado de Registro e
        Licenciamento de Veiculo efou especificações do fabricante.<br>
        b) entregar a direção do veiculo a pessoa não indicada neste contrato e que venha a sofrer acidente, mesmo que este não tenha
        sido provocado pelo condutor.<br>
        c) trafegar por vias publicas, rodovias ou caminhos sem condições de trafego e, em consequência, provocar dano ao veiculo ou
        acidente com terceiro.<br>
        d) infringir qualquer dispositivo do Código Nacional de Transito e, em decorrência disso, provocar acidente com terceiro ou dano
        ao veiculo, principalmente no caso de velocidade imprimida acima do pemitido para o local.<br>
        e) Outras modalidades de uso do veiculo que possam se configurar como mau uso do mesmo, comprovado esse através de laudo
        de oficina mecânica ou funilaria especializada, testemunhas ou outros meios legais.<br><Br>
        4.1. Configurando-se qualquer das hipóteses da presente cláusula, e consequentemente perdendo o cliente a proteção
        LOCADORA, arcará com todos os prejuízos a quer causa à locadora efou terceiros prejudicados, inclusive danos pessoais dos
        passageiros do carro alugado efou terceiros, sem prejuízo das coberturas previstas no DPVAT. Ainda, pagará o cliente, a título de
        lucro cessante, 70% (setenta por cento) do maior valor da diária contratada, pelo período que permanecer o veiculo da locadora
        em conserto, até o limite de 30 (trinta) diárias.<br><br>
        4.2. Em decorrência deste contrato, quando não contratar qualquer tipo de proteção, ou ainda, quando perder a proteção
        LOCADORA, nos ternos deste Contrato, a cliente isenta desde já a locadora de responsabilidades civis a qualquer titulo, bem
        assim de figurar como parte passiva em qualquer demanda oriunda de eventos que envolvam o carro alugado através deste
        Contrato, ônus que o cliente assume "de per si' e exclusivamente. Na hipótese da LOCADORA ser acionada, isolada ou
        solidariamente, ficará autorizada a chamar o cliente ao processo, para assumir a demanda ou a lide, ou ainda, para preservar o
        direito regressivo.<br><br>
        4.3. No caso de reparação do veiculo, esta atingir 70% (setenta por cento) do seu valor comercial, considerar-se-á como tendo
        oconido perda total do mesmo, tornando-se como seu valor aquele estabelecido pela Revista Quatro Rodas ou a média
        encontrada pelos jornais especializados em veículos ou avaliação da Concessionária da marca.<br><br>
        4.4. A não devolução do veiculo na data determinada no Demonstrativo do Contrato de Aluguel de Veículos (ANEXO 1) sem
        expressa autorização da locadora, igualmente configurará perda da proteção LOCADORA, para efeitos de indenização civil por
        danos a terceiros ou ao veiculo alugado, perdurando essa responsabilidade até a efetiva devolução.<br><br>
        <b>DAS DISPOSIÇÕES GERAIS</b><br><br>
        5.1.A LOCADORA não responderá por quaisquer custos, pagamentos ou indenização, cabendo-lhe, como locatário/usuário arcar
        com tais ônus, nos pleitos judiciais ou extrajudiciais decorrentes de eventos que envolvam o carro alugado, ocorridos no período
        em que ele esteve sob sua guarda e posse, direta ou indiretamente.<br><br>
        5.2. As eventuais tolerâncias da LOCADORA para com o diente no cumprimento das obrigações ajustadas através deste contrato
        não importam em novação, permanecendo integras as cláusulas e condições deste contrato.<br><br>
        5.3. Nos casos de inadimplência, ficará sujeito o cliente, ao pagamento de multa contratual de 2% (dois por cento), bem como
        honorários advocatícios na base de 20% (vinte por cento) do valor total do debito, além de juros de 1% (um por cento) ao mês e correção monetária.<br><Br>
        5.4. O Foro para qualquer procedimento judicial relativo com o presente contrato será o da cidade da LOCADORA, com renuncia
        expressa de qualquer outro, por mais privilegiado que possa ser, sem prejuízo da possibilidade de requerimento, pela locadora, de
        medidas cautelares em outro Foro.<br><br>
        E, por estarem justos e contratados, assinam o presente contrato em 2 (duas) vias de igual teor e forma, para que produza os
        efeitos legais.</p>
</div><br><br>

        <div style="text-align: center; font-size: 12">Lajedo, {{ $dataAtual->isoFormat('DD MMMM YYYY') }}<br><br><br><br>

            ___________________________________________________________<br>
            LOCATÁRIO: {{$locacao->Cliente->nome}}<br><Br><br><br>

            ___________________________________________________________<br>
            LOCADOR: LOCADORA DE VEICULOS LUCENA- EIRELI-ME



        </div>

</body>
</html>
