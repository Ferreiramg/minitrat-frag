CREATE TABLE `wp_mi_solicitacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_empree` char(1) DEFAULT NULL,
  `fase_empree` char(1) DEFAULT NULL,
  `padrao` char(1) DEFAULT NULL,
  `modelo_empree` varchar(255) DEFAULT NULL,
  `total_empree` int(11) DEFAULT NULL,
  `distancia_ponto` float DEFAULT NULL,
  `turno_um` json DEFAULT NULL,
  `turno_dois` json DEFAULT NULL,
  `turno_tres` json DEFAULT NULL,
  `op_turno_um` json DEFAULT NULL,
  `op_turno_dois` json DEFAULT NULL,
  `op_turno_tres` json DEFAULT NULL,
  `razao` varchar(255) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL,
  `cidade` varchar(255) DEFAULT NULL,
  `receber_notificacao` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;