<?php
$quantidadeMeses = isset($_POST['quantidadeMeses'])? (int) $_POST['quantidadeMeses']:0;
$numeroNovoContratosPorMes = isset($_POST['numeroNovoContratosPorMes'])? (int)$_POST['numeroNovoContratosPorMes']:0;
$valorDeCadaContrato = isset($_POST['valorDeCadaContrato'])? (float)$_POST['valorDeCadaContrato'] : 0;
echo "<form method='post'>";
echo "<label>Quantidade de mês para simular: </label>";
echo "<input type='text' name='quantidadeMeses' value='{$quantidadeMeses}'/><br>";
echo "<label>Número de novos contratos: </label>";
echo "<input type='text' name='numeroNovoContratosPorMes' value='{$numeroNovoContratosPorMes}'/><br>";
echo "<label>Valor de cada contrato: </label>";
echo "<input type='number' name='valorDeCadaContrato' value='{$valorDeCadaContrato}'/><br>";
echo "<button type='submit'>Mostrar resultado</button>";
echo "</form>";
if(!isset($_POST['quantidadeMeses'])){
 exit;
}
if(!$quantidadeMeses){
 exit("Informe a quantidade de meses para simular");
}
$transacoes = [];
$dataInicio = $mesAtual = date('Y-01-01');
while ($mesAtual <= date('Y-m-d', strtotime("+" . ($quantidadeMeses - 1) . "months", strtotime($dataInicio)))) {
 for ($cliente = 1; $cliente <= $numeroNovoContratosPorMes; $cliente++) {
 $transacoes[] = [
 'data' => $mesAtual,
 'cliente' => 'cliente-' . date('MY', strtotime($mesAtual)) . '-' . $cliente,
 'valor' => $valorDeCadaContrato
 ];
 }
 $mesAtual = date('Y-m-d', strtotime("+1months", strtotime($mesAtual)));
}
// Escreva seu código aqui, sem alterar o código acima
$numeroMaxMesPorTabela = 6;
if (sizeof($transacoes) == 0) {
    exit("Não possui transições para simular");
}
$tabelas = [];
$tabelas[] = [];
$indiceTabela = 0;
$indiceMes = -1;
$ultimoMes = '';
$valorTotalAcumulado = 0;
for ($i=0; $i < sizeof($transacoes); $i++) {
    if (sizeof($tabelas[$indiceTabela]) == $numeroMaxMesPorTabela
        && $ultimoMes != date("Y-m", strtotime($transacoes[$i]['data']))
    ) {
        $tabelas[] = [];
        $indiceTabela++;
        $indiceMes = -1;
    }

    if ($ultimoMes != date("Y-m", strtotime($transacoes[$i]['data']))) {
        $ultimoMes = date("Y-m", strtotime($transacoes[$i]['data']));
        $indiceMes++;
        $valorTotalAcumulado += $transacoes[$i]['valor'];
        $tabelas[$indiceTabela][] = [
            'mes' => $ultimoMes,
            'qtdContrato' => 1,
            'totalMes' => $transacoes[$i]['valor'],
            'acumulado' => $valorTotalAcumulado
        ];
    } else {
        $valorTotalAcumulado += $transacoes[$i]['valor'];
        $tabelas[$indiceTabela][$indiceMes]['qtdContrato']++;
        $tabelas[$indiceTabela][$indiceMes]['totalMes'] += $transacoes[$i]['valor'];
        $tabelas[$indiceTabela][$indiceMes]['acumulado'] += $transacoes[$i]['valor'];
    }
}

for ($iTabela=0; $iTabela < sizeof($tabelas); $iTabela++) {
    $footTotalMes = '';
    $footTotalAcumulado = '';
    $acresimo = ($numeroMaxMesPorTabela * $iTabela);
    echo '<br><br>';
    echo '<table width="100%" border="1">';
    echo '<thead>';
    echo '<tr>';
    echo "<th style='text-align: left;'>Meses</th>";
    for ($iHeader=0; $iHeader < sizeof($tabelas[$iTabela]); $iHeader++) {
        $numMes = ($iHeader+1) + $acresimo;
        $valor = number_format($tabelas[$iTabela][$iHeader]['totalMes'], 2, ',', '.');
        $acumulado = number_format($tabelas[$iTabela][$iHeader]['acumulado'], 2, ',', '.');
        echo "<th style='text-align: left;'>{$numMes}º Mês</th>";
        $footTotalMes .= "<td style='color: green'>R$ {$valor}</td>";
        $footTotalAcumulado .= "<td style='color: green'>R$ {$acumulado}</td>";
    }
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    for ($iLinha=0; $iLinha < sizeof($tabelas[$iTabela]) + $acresimo; $iLinha++) {
        echo "<tr>";
        echo "<td></td>";
        for ($iCell=0; $iCell < sizeof($tabelas[$iTabela]); $iCell++) {
            $iTabelaLocal = $iTabela - ((int) ($acresimo / $numeroMaxMesPorTabela) - (int) ($iLinha / $numeroMaxMesPorTabela));
            $iLinhaLocal = $iLinha - ($iTabelaLocal * $numeroMaxMesPorTabela);
            $vlrMes = $iCell + $acresimo >= $iLinha ? $tabelas[$iTabelaLocal][$iLinhaLocal]['qtdContrato'] : '';
            echo "<td>{$vlrMes}</td>";
        }
        echo "</tr>";
    }
    echo '<tr>';
    echo '<td style="color: green">VALOR TOTAL ADESÕES NO MÊS</td>';
    echo $footTotalMes;
    echo '</tr>';
    echo '<tr>';
    echo '<td style="color: green">VALOR TOTAL ADESÕES ACUMULADO</td>';
    echo $footTotalAcumulado;
    echo '</tr>';
    echo '</tbody>';
    echo '</table>';
}
?>