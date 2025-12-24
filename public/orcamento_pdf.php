<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";
require __DIR__ . "/../libs/fpdf.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Orçamento inválido");
}

// Buscar dados do orçamento
$stmt = $pdo->prepare("
    SELECT 
        o.*,
        c.nome AS cliente,
        c.email,
        c.telefone
    FROM orcamentos o
    LEFT JOIN clientes c ON c.id = o.cliente_id
    WHERE o.id = ?
");
$stmt->execute([$id]);
$o = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$o) {
    die("Orçamento não encontrado");
}

// Criar PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

// Cabeçalho
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'ORÇAMENTO',0,1,'C');
$pdf->Ln(4);

// Dados do orçamento
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Orçamento #: '.$o['id'],0,1);
$pdf->Cell(0,8,'Data: '.date('d/m/Y', strtotime($o['criado_em'])),0,1);
$pdf->Cell(0,8,'Status: '.strtoupper($o['status']),0,1);
$pdf->Ln(4);

// Cliente
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Cliente',0,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Nome: '.($o['cliente'] ?? 'Não informado'),0,1);
$pdf->Cell(0,8,'Email: '.($o['email'] ?? '-'),0,1);
$pdf->Cell(0,8,'Telefone: '.($o['telefone'] ?? '-'),0,1);
$pdf->Ln(4);

// Detalhes do projeto
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Detalhes do Projeto',0,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Tipo de Projeto: '.ucfirst($o['tipo_projeto']),0,1);
$pdf->Cell(0,8,'Design: '.ucfirst($o['tipo_design']),0,1);
$pdf->Cell(0,8,'Urgência: '.ucfirst($o['urgencia']),0,1);
$pdf->Ln(4);

// Descrição
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Descrição',0,1);
$pdf->SetFont('Arial','',12);
$pdf->MultiCell(0,8, $o['descricao'] ?: '—');
$pdf->Ln(4);

// Valores
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Valores',0,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Valor do Projeto: R$ '.number_format($o['valor_estimado'],2,',','.'),0,1);

// Margem (uso interno — pode ocultar se quiser)
$pdf->Cell(0,8,'Margem estimada: '.number_format($o['margem_estimada'],2,',','.' ).'%',0,1);

$pdf->Ln(10);

// Observações finais
$pdf->SetFont('Arial','I',10);
$pdf->MultiCell(
    0,
    7,
    "Este orçamento tem validade de 15 dias.\nOs valores e prazos podem ser ajustados mediante aprovação.\n\nAMDigital - Soluções Digitais",
    0,
    'C'
);

// Saída
$pdf->Output("I", "orcamento_{$o['id']}.pdf");
