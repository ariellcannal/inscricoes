<?php
if (! function_exists('diferencaDatas')) {
    function diferencaDatas($data1,$data2,$return_type='days'){
        // Caso seja informado uma data do MySQL do tipo DATETIME - aaaa-mm-dd 00:00:00
        // Transforma para DATE - aaaa-mm-dd
        $data1 = substr( $data1, 0, 10 );
        $data2 = substr( $data2, 0, 10 );
        // Se a data estiver no formato brasileiro: dd/mm/aaaa
        // Converte-a para o padrão americano: aaaa-mm-dd
        if ( preg_match( "@/@", $data1 ) == 1 ) {
            $data1 = implode( "-", array_reverse( explode( "/", $data1 ) ) );
        }
        if ( preg_match( "@/@", $data2 ) == 1 ) {
            $data2 = implode( "-", array_reverse( explode( "/", $data2 ) ) );
        }
        $data_inicio = new DateTime($data1);
        $data_fim = new DateTime($data2);
        

        $dateInterval = $data_inicio->diff($data_fim);
        $diferenca = $dateInterval->$return_type;
        if($diferenca < 0){
            $diferenca = $diferenca * -1;
        }
        return $diferenca;
    }
}
if (! function_exists('somarDiasUteis')) {

    function somarDiasUteis( $str_data, $int_qtd_dias_somar, $feriados ){
        // Caso seja informado uma data do MySQL do tipo DATETIME - aaaa-mm-dd 00:00:00
        // Transforma para DATE - aaaa-mm-dd
        $str_data = substr( $str_data, 0, 10 );
        // Se a data estiver no formato brasileiro: dd/mm/aaaa
        // Converte-a para o padrão americano: aaaa-mm-dd
        if ( preg_match( "@/@", $str_data ) == 1 ) {
            $str_data = implode( "-", array_reverse( explode( "/", $str_data ) ) );
        }
        
        $array_data = explode( '-', $str_data );
        $count_days = 0;
        $int_qtd_dias_uteis = 0;
        while ( $int_qtd_dias_uteis < $int_qtd_dias_somar ) {
            $count_days++;
            $day = date( 'Y-m-d', strtotime( '+' . $count_days . 'day', strtotime( $str_data ) ) );
            if ( ($dias_da_semana = gmdate( 'w', strtotime( '+' . $count_days . ' day', gmmktime( 0, 0, 0, $array_data[1], $array_data[2], $array_data[0] ) ) ) ) != '0' && $dias_da_semana != '6' && !in_array( $day, $feriados ) ) {
                $int_qtd_dias_uteis++;
            }
        }
        return gmdate( 'Y-m-d', strtotime( '+' . $count_days . ' day', strtotime( $str_data ) ) );
    }
}