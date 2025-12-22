<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";
require __DIR__ . "/../libs/fpdf.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    die("OS não encontrada");
}

// Buscar dados da OS
$stmt = $pdo->prepare("
    SELECT 
        os.*,
        c.nome AS cliente,
        c.email,
        c.telefone,
        p.nome AS projeto
    FROM ordens_servico os
    JOIN clientes c ON c.id = os.cliente_id
    LEFT JOIN projetos p ON p.id = os.projeto_id
    WHERE os.id = ?
");
$stmt->execute([$id]);
$os = $stmt->fetch();

if (!$os) {
    die("OS não encontrada");
}

// Criar PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

// Cabeçalho
$pdf->Cell(0,10,'ORDEM DE SERVIÇO',0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','',12);

// Dados principais
$pdf->Cell(0,8,'OS #: '.$os['id'],0,1);
$pdf->Cell(0,8,'Status: '.strtoupper($os['status']),0,1);
$pdf->Cell(0,8,'Data: '.date('d/m/Y', strtotime($os['criado_em'])),0,1);
$pdf->Ln(4);

// Cliente
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Cliente',0,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Nome: '.$os['cliente'],0,1);
$pdf->Cell(0,8,'Email: '.($os['email'] ?? '-'),0,1);
$pdf->Cell(0,8,'Telefone: '.($os['telefone'] ?? '-'),0,1);
$pdf->Ln(4);

// Projeto
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Projeto',0,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,$os['projeto'] ?? '—',0,1);
$pdf->Ln(4);

// Descrição
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Descricao do Servico',0,1);
$pdf->SetFont('Arial','',12);
$pdf->MultiCell(0,8,$os['descricao']);
$pdf->Ln(4);

// Valor
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Valor do Servico',0,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'R$ '.number_format($os['valor'],2,',','.'),0,1);

// Rodapé
$pdf->Ln(15);
$pdf->SetFont('Arial','I',10);
$pdf->Cell(0,10,'Documento gerado pelo sistema AMDigital ERP',0,1,'C');

// Saída
$pdf->Output('I', 'OS_'.$os['id'].'.pdf');


