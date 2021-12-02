<?php

namespace Skynix\Remessax\Tests;

use Skynix\Remessax\Remessa\Sicoob240 as Remessax_Remessa_Sicoob240;
use Skynix\Remessax\Config\Sicoob240 as Sicoob240Config;
use Skynix\Remessax\Remessax_TituloList;
use Skynix\Remessax\TituloList;

class RemessaSiscobbTest extends TestCase
{

    public function testAddDays()
    {
        $data = [

        ];


        $config = new Sicoob240Config(null,null);
        $titulos = new TituloList($data);

        $res = new Remessax_Remessa_Sicoob240($config,$titulos);
        $data = $res->addDays('2021-12-01',2);
     
        $this->assertEquals('03122021',$data);

        $data = $res->addDays('2021-11-30',2);
     
        $this->assertEquals('02122021',$data);
    }

    public function testNossoNumero(): void
    {
        $data = [

        ];

        $config = new Sicoob240Config(null,null);
        $titulos = new TituloList($data);

        $res = new Remessax_Remessa_Sicoob240($config,$titulos);
        $cooperativa = '0001';
        $cliente = '19';
        $titulo = '21';

        $response = $res->nossoNumero($cooperativa,$cliente,$titulo);
        $this->assertEquals(intval($response),8);

        $agencia = '3357';
        $convenio = '295701';

        $testes = [
            344=>3,
            345=>0,
            346=>8,
            346=>8,
            347=>5,
            348=>2,
            348=>2,
            349=>0,
            350=>8,
            350=>8,
            351=>5,
            364=>0,
        ];

        foreach($testes as $titulo=>$dv)
        {
            $response = $res->nossoNumero($agencia,$convenio,$titulo);
            $this->assertEquals($titulo.$dv,$titulo.$response);
        }

    }

}