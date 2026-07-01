<?php

namespace Database\Seeders;

use App\Models\Setor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SetorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Setores da Comunhão Espírita de Brasília — Anexo ao Regimento, aprovado em 02.06.2025.
     */
    public function run(): void
    {
        $setores = [
            // Governança
            'Assembleia Geral',
            'Conselho Editorial',
            'Conselho Diretor',
            'Conselho Fiscal',
            'Comissões Permanentes',
            'Presidência',
            'Diretoria Geral',

            // DAF - Diretoria Administrativa e Financeira
            'DAF - Diretoria Administrativa e Financeira',
            'CODAM - Coordenação Administrativa DAF',
            'DIADM - Divisão Administrativa DAF',
            'DIFIN - Divisão Financeira',
            'DINEG - Divisão de Negócios',

            // DAE - Diretoria de Assistência Espiritual
            'DAE - Diretoria de Assistência Espiritual',
            'CODAE - Coordenação Administrativa DAE',
            'DIDES - Divisão de Desobsessão',
            'DITAD - Divisão de Tratamento de Mediunidade',
            'DIEME - Divisão de Educação de Mediunidade',
            'DIPAH - Divisão de Passe e Harmonização',
            'DIAMO - Divisão de Apoio ao Médium em Desenvolvimento e Educação da Mediunidade',

            // DAO - Diretoria de Atendimento e Orientação
            'DAO - Diretoria de Atendimento e Orientação',
            'CODAO - Coordenação Administrativa DAO',
            'DIVAP - Divisão de Atendimento ao Público',
            'DIVAF - Divisão de Atendimento Fraterno',
            'DIVAT - Divisão de Atendimento Específico e Formação',

            // DPS - Diretoria da Promoção Social
            'DPS - Diretoria da Promoção Social',
            'COPPS - Coordenação Administrativa DPS',
            'DIAFA - Divisão de Acompanhamento à Família',
            'DIOFI - Divisão de Oficinas',
            'DIPES - Divisão de Assistência a Pessoas em Situação de Rua',
            'DIAFRA - Divisão de Fraterna',

            // DED - Diretoria de Estudos Doutrinários
            'DED - Diretoria de Estudos Doutrinários',
            'CODED - Coordenação Administrativa DED',
            'DIVES - Divisão de Estudo Sistematizado da Doutrina Espírita',
            'DIPAD - Divisão do Programa de Aperfeiçoamento e Doutrina Espírita',
            'DIMOC - Divisão de Mocidade Espírita da Comunhão',
            'DIPAP - Divisão de Pesquisa e Aperfeiçoamento',
            'DIESP - Divisão de Especialização',
            'DIFTE - Divisão de Formação do Trabalhador Espírita',

            // DIJ - Diretoria de Infância e Juventude
            'DIJ - Diretoria de Infância e Juventude',
            'DIRME - Divisão de Recursos e Meios',
            'DEMAT - Divisão de Evangelização da Maternal',
            'DEINF - Divisão de Evangelização da Infância',
            'DEJUV - Divisão de Evangelização da Juventude',
            'DEFAM - Divisão de Evangelização da Família',

            // DAC - Diretoria de Arte e Cultura
            'DAC - Diretoria de Arte e Cultura',
            'CODAC - Coordenação Administrativa DAC',
            'DITEA - Divisão de Teatro',
            'DIDAN - Divisão de Dança',
            'DIMUS - Divisão de Música',
            'DICIN - Divisão de Cinema',
            'DIPPI - Divisão de Pintura e Poesia',
            'DIPRA - Divisão de Produção Artística',

            // Assessorias
            'APV - Assessoria da Pomada do Vovô',
            'AAD - Assessoria de Assuntos Doutrinários',
            'ADI - Assessoria de Desenvolvimento Institucional',
            'ACE - Assessoria de Comunicação e Eventos',
            'APE - Assessoria de Planejamento Estratégico',
            'ATI - Assessoria de Tecnologia da Informação',
            'AJU - Assessoria Jurídica',
            'AME - Assessoria de Estudos e Aplicações de Medicina Espiritual',
            'Ouvidoria',
        ];

        foreach ($setores as $nome) {
            Setor::create(['nome' => $nome]);
        }
    }
}
