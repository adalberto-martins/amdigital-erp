<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";
require __DIR__ . "/../libs/fpdf.php";

/* Evita qualquer saída antes do PDF */
ob_clean();

/* Função correta para FPDF */
function pdf($text) {
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text ?? '');
}

$id = $_GET['id'] ?? null;
if (!$id) {
    exit;
}

/* BUSCA ORÇAMENTO */
$stmt = $pdo->prepare("
    SELECT 
        o.*,
        c.nome      AS cliente,
        c.email     AS email,
        c.telefone  AS telefone
    FROM orcamentos o
    LEFT JOIN clientes c ON c.id = o.cliente_id
    WHERE o.id = ?
");
$stmt->execute([$id]);
$o = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$o) {
    exit;
}

/* CRIA PDF */
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

/* CABEÇALHO */
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,pdf('ORÇAMENTO COMERCIAL'),0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,pdf('Orçamento nº: ') . $o['id'],0,1);
$pdf->Cell(0,8,pdf('Data: ') . date('d/m/Y', strtotime($o['criado_em'])),0,1);
$pdf->Ln(5);

/* CLIENTE */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,pdf('Dados do Cliente'),0,1);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,pdf('Nome: ') . pdf($o['cliente']),0,1);
$pdf->Cell(0,8,pdf('Email: ') . pdf($o['email'] ?: '-'),0,1);
$pdf->Cell(0,8,pdf('Telefone: ') . pdf($o['telefone'] ?: '-'),0,1);
$pdf->Ln(5);

/* PROJETO */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,pdf('Descrição do Projeto'),0,1);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,pdf('Tipo de Projeto: ') . pdf(ucfirst($o['tipo_projeto'])),0,1);
$pdf->Cell(0,8,pdf('Tipo de Design: ') . pdf(ucfirst($o['tipo_design'])),0,1);
$pdf->Cell(0,8,pdf('Urgência: ') . pdf(ucfirst($o['urgencia'])),0,1);
$pdf->Ln(4);

/* DESCRIÇÃO */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,pdf('Detalhes do Serviço'),0,1);

$pdf->SetFont('Arial','',12);
$pdf->MultiCell(0,8,pdf($o['descricao']));
$pdf->Ln(4);

/* VALOR */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,pdf('Valor do Investimento'),0,1);

$pdf->SetFont('Arial','',12);
$pdf->Cell(
    0,
    10,
    pdf('R$ ') . number_format($o['valor_estimado'], 2, ',', '.'),
    0,
    1
);

/* RODAPÉ */
$pdf->Ln(15);
$pdf->SetFont('Arial','I',10);
$pdf->Cell(
    0,
    10,
    pdf('Proposta gerada pelo sistema AMDigital • Documento sem valor fiscal'),
    0,
    1,
    'C'
);

/* SAÍDA */
$pdf->Output('I', 'orcamento_'.$o['id'].'.pdf');
exit;




