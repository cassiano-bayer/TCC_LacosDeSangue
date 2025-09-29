<?php
// Script para inserir os hospitais iniciais no banco de dados
// Execute este arquivo uma vez para inserir os dados iniciais

include_once('config.php');

// Dados dos hospitais do area_usuario.php
$hospitais_iniciais = [
    [
        'nome' => 'Grupo Hospitalar Conceição',
        'tipos_necessarios' => 'O+,A-,B-',
        'urgencia' => 'alta',
        'localizacao' => 'Zona Norte',
        'telefone' => '(51) 3333-1111'
    ],
    [
        'nome' => 'Hospital Moinhos de Vento',
        'tipos_necessarios' => 'A+,B+,AB+',
        'urgencia' => 'media',
        'localizacao' => 'Zona Norte',
        'telefone' => '(51) 3333-2222'
    ],
    [
        'nome' => 'Santa Casa',
        'tipos_necessarios' => 'AB-,A+,O-',
        'urgencia' => 'alta',
        'localizacao' => 'Zona Sul',
        'telefone' => '(51) 3333-3333'
    ],
    [
        'nome' => 'Hospital São Lucas',
        'tipos_necessarios' => 'O+,O-,A-',
        'urgencia' => 'critica',
        'localizacao' => 'Zona Leste',
        'telefone' => '(51) 3333-4444'
    ],
    [
        'nome' => 'Hospital de Clínicas',
        'tipos_necessarios' => 'B+,AB+,A+',
        'urgencia' => 'media',
        'localizacao' => 'Centro',
        'telefone' => '(51) 3333-5555'
    ]
];

// Limpar tabela primeiro (opcional)
$sql_limpar = "DELETE FROM hospitais";
$conexao->query($sql_limpar);

// Inserir hospitais
foreach ($hospitais_iniciais as $hospital) {
    $sql = "INSERT INTO hospitais (nome, tipos_necessarios, urgencia, localizacao, telefone) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("sssss", 
            $hospital['nome'], 
            $hospital['tipos_necessarios'], 
            $hospital['urgencia'], 
            $hospital['localizacao'], 
            $hospital['telefone']
        );
        
        if ($stmt->execute()) {
            echo "Hospital '{$hospital['nome']}' inserido com sucesso!<br>";
        } else {
            echo "Erro ao inserir hospital '{$hospital['nome']}': " . $stmt->error . "<br>";
        }
        
        $stmt->close();
    } else {
        echo "Erro na preparação da consulta: " . $conexao->error . "<br>";
    }
}

echo "<br><strong>Inserção de hospitais concluída!</strong><br>";
echo "<a href='sistema.php'>Voltar ao Sistema</a>";

$conexao->close();
?>