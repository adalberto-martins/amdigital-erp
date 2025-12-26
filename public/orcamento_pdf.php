<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";
require __DIR__ . "/../libs/fpdf.php";

/* =========================
   EVITA QUALQUER SAÍDA
========================= */
ob_clean();

/* =========================
   FUNÇÃO CORRETA PARA FPDF
========================= */
function pdf($text) {
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text ?? '');
}

/* =========================
   ID DO ORÇAMENTO
========================= */
$id = $_GET['id'] ?? null;
if (!$id) {
    exit;
}

/* =========================
   BUSCA ORÇAMENTO
========================= */
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

/* =========================
   CAMINHOS DE IMAGEM
========================= */
$logo       = __DIR__ . '/assets/logo.png';
$assinatura = __DIR__ . '/assets/assinatura.png';

/* =========================
   CRIA PDF
========================= */
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 25);

/* =========================
   CABEÇALHO COM LOGO
========================= */
if (file_exists($logo)) {
    $pdf->Image($logo, 10, 10, 35);
}

$pdf->SetY(20);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,pdf('ORÇAMENTO COMERCIAL'),0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,pdf('Orçamento nº: ') . $o['id'],0,1);
$pdf->Cell(0,8,pdf('Data: ') . date('d/m/Y', strtotime($o['criado_em'])),0,1);
$pdf->Ln(6);

/* =========================
   DADOS DO CLIENTE
========================= */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,pdf('Dados do Cliente'),0,1);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,pdf('Nome: ') . pdf($o['cliente']),0,1);
$pdf->Cell(0,8,pdf('Email: ') . pdf($o['email'] ?: '-'),0,1);
$pdf->Cell(0,8,pdf('Telefone: ') . pdf($o['telefone'] ?: '-'),0,1);
$pdf->Ln(6);

/* =========================
   DADOS DO PROJETO
========================= */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,pdf('Descrição do Projeto'),0,1);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,pdf('Tipo de Projeto: ') . pdf(ucfirst($o['tipo_projeto'])),0,1);
$pdf->Cell(0,8,pdf('Tipo de Design: ') . pdf(ucfirst($o['tipo_design'])),0,1);
$pdf->Cell(0,8,pdf('Urgência: ') . pdf(ucfirst($o['urgencia'])),0,1);
$pdf->Ln(4);

/* =========================
   DESCRIÇÃO DO SERVIÇO
========================= */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,pdf('Detalhes do Serviço'),0,1);

$pdf->SetFont('Arial','',12);
$pdf->MultiCell(0,8,pdf($o['descricao']));
$pdf->Ln(5);

/* =========================
   VALOR COMERCIAL
========================= */
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

/* =========================
   ASSINATURA DIGITAL
========================= */
$pdf->Ln(15);
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,8,pdf('Adalberto Martins'),0,1);

if (file_exists($assinatura)) {
    $pdf->Image($assinatura, 10, $pdf->GetY(), 50);
    $pdf->Ln(25);
}

$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,8,pdf('AMDigital • Tecnologia & Automação'),0,1);

/* =========================
   RODAPÉ
========================= */
$pdf->Ln(10);
$pdf->SetFont('Arial','I',10);
$pdf->Cell(
    0,
    10,
    pdf('www.amdigital.net.br | WhatsApp: (19) 9xxxx-xxxx'),
    'T',
    1,
    'C'
);

/* =========================
   SAÍDA FINAL
========================= */
$pdf->Output('I', 'orcamento_'.$o['id'].'.pdf');
exit;





