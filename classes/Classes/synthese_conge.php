<?php
    define('FIRST_LINE', 9);
    define('SPACE_WITH_TITLE',3);
    define('HEIGHT_SUBTITLE', 100);
    //IMPORTANT :  Il ne faut PAS inclure la connexion mais la mettre comme ci-dessous car bug connu avec PHPEXCEL (fichier généré via le navigateur inexploitable !!!!)
    try
    {
        $bdd = new PDO('mysql:host=localhost;dbname=congespharma', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    }
    catch (PDOException $e)
    {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }
    
    if (!isset($_SESSION))
    {
        session_start();
    }
    header('Content-Type: text/html; charset=UTF-8');
    
    include 'PHPExcel.php';
    include 'PHPExcel/Writer/Excel2007.php';
    include '../Encryption.class.php';
    
    if(codeIsCorrect($bdd))
    {
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('Synthese conge');
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        
        //ajout du titre
        $styleTitle= array(
                        'font'  => array(
                            'bold'  => true,
                            'size'  => 25,
                            'name'  => 'Verdana'
                        )
                    );
        $sheet->setCellValue('A'.FIRST_LINE,'Synthèse des congés réservés');
        $sheet->getStyle('A'.FIRST_LINE)->applyFromArray($styleTitle);
        $sheet->mergeCells('A'.FIRST_LINE.':F'.FIRST_LINE);
        
        $sql = $bdd->prepare("SELECT id, libelle
                              FROM service
                              JOIN service_has_utilisateurs
                              ON service_id = id
                              WHERE utilisateurs_id = :id");
        
        $sql->execute(array(':id' => $_GET['id']));  
        $res = $sql->fetchAll(PDO::FETCH_OBJ);
        $code = 
        $subTitle = "";
        for($i = 0; $i < count($res); $i++)
        {
            $subTitle .= (($i > 0)?' et ': ' ') . 'service ' .$res[$i]->libelle;
            $services[] = $res[$i]->id;
        }
        
        $sheet->setCellValue('A'.(FIRST_LINE+1), $subTitle);
        $sheet->getStyle('A'.(FIRST_LINE+1))->applyFromArray($styleTitle);
        $sheet->mergeCells('A'.(FIRST_LINE+1).':F'.(FIRST_LINE+1));
        $sheet->getStyle('A'.(FIRST_LINE+1))->getAlignment()->setWrapText(true);
        $sheet->getStyle('A'.(FIRST_LINE+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(FIRST_LINE+1)->setRowHeight(HEIGHT_SUBTITLE);
        
        //ajout nom des colonnes
        $sheet->setCellValue('A'.((FIRST_LINE + SPACE_WITH_TITLE)), 'Date')
              ->setCellValue('C'.((FIRST_LINE + SPACE_WITH_TITLE)), 'Nom')
              ->setCellValue('D'.((FIRST_LINE + SPACE_WITH_TITLE)), 'Prénom')
              ->setCellValue('E'.((FIRST_LINE + SPACE_WITH_TITLE)), 'Email')
              ->setCellValue('F'.((FIRST_LINE + SPACE_WITH_TITLE)), 'Commentaire');
        
        //j'ajoute le logo de la Faculté
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('logo PHARMA');
        $objDrawing->setDescription('logo PHARMA');
        $objDrawing->setPath('../../images/pharma.png');
        $objDrawing->setHeight(100);
        $objDrawing->setCoordinates('B1');
        $objDrawing->setOffsetX(-10);
        $objDrawing->setWorksheet($sheet);
               
        //redimension des colonnes
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(5);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(50);
        
        //generation des bordures
        $generalBorder = array(
                            'borders'  => array(
                                'allborders' => array(
                                    'style' => PHPExcel_style_Border::BORDER_THIN
                                )
                            ));
        $headerBorder = array(
                            'borders' => array(
                               'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                                ) 
                            )
                        );
        $separateDateBorder = array(
                                'borders' => array(
                                    'top' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                                    )
                                )
                            );
        $noBorder = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_NONE
                            )
                        )
            
        );
        
        $sqlRequest = "SELECT r.jour,
                              r.id_user,
                              r.commentaire,
                              u.nom,
                              u.prenom,
                              u.email 
                       FROM reservations r
                       LEFT JOIN utilisateurs u
                       ON r.id_user = u.id
                       LEFT JOIN service_has_utilisateurs shu
                       ON r.id_user = shu.utilisateurs_id
                       WHERE (shu.service_id = " . $services[0];
        
        if(count($services) > 1)
        {
            $sqlRequest .= " OR shu.service_id = " . $services[1];
        }
        
        $sqlRequest .= ") GROUP BY r.id
                         ORDER BY SUBSTR(r.jour,1,10), r.id_user";
                         
        $sql = $bdd->prepare($sqlRequest);
        $sql->execute();   
        $result = $sql->fetchAll(PDO::FETCH_OBJ);
        
        $sheet->getStyle('A'.((FIRST_LINE + SPACE_WITH_TITLE)).':F'.((FIRST_LINE + SPACE_WITH_TITLE)))->applyFromArray($headerBorder);
        $sheet->getStyle('B'.((FIRST_LINE + SPACE_WITH_TITLE)+1).':F'.(count($result)+(FIRST_LINE + SPACE_WITH_TITLE)))->applyFromArray($generalBorder);
        
        $line = FIRST_LINE + SPACE_WITH_TITLE;
        for($i = 0; $i < count($result); $i++)
        {
            $line++;
            $date = ConverterToDate($result[$i]->jour);
            if(!isset($current_date) || $current_date != $date)
            {
                if(isset($current_date))
                {
                    $sheet->getStyle('A'.$line.':F'.$line)->applyFromArray($separateDateBorder);
                }
                $sheet->setCellValue('A'.$line, FormatDate($result[$i]->jour));
                $current_date = $date;
            }
    
            if($i != count($result)-1 && holidayAllDay($date,$result[$i]->id_user, $result[$i+1]))
            {
                $i++;
            }
            else
            {
                $sheet->setCellValue('B'.$line, getAmPm($result[$i]->jour));
            }
            $sheet->setCellValue('C'.$line, $result[$i]->nom)
                  ->setCellValue('D'.$line, $result[$i]->prenom)
                  ->setCellValue('E'.$line, $result[$i]->email)
                  ->setCellValue('F'.$line, $result[$i]->commentaire);
        }
        
        $sheet->getStyle('A'. ($line+1) .':F'.(count($result)+(FIRST_LINE + SPACE_WITH_TITLE)))->applyFromArray($noBorder);
        
        $writer = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $writer->save(str_replace('.php', '.xlsx', __FILE__));
        
        echo 'Exportation OK, veuillez cliquer -> <a href="synthese_conge.xlsx"><img src="../../images/excel.png" /></a> pour ouvrir ou sauvegarder le document';
    }
    else
    {
        echo 'erreur';
        //header('Location: ../../synthese.php');
    }
    
    function ConverterToDate($string)
    {
        $composant_date = explode('_',$string);
        return new DateTime($composant_date[0].'-'.$composant_date[1].'-'.$composant_date[2]);
    }
    
    function FormatDate($string)
    {
        $composant_date = explode('_',$string);
        return $composant_date[2].'/'.$composant_date[1].'/'.$composant_date[0];
    }
    
    function getAmPm($string)
    {
        $composant_date = explode('_',$string);
        return $composant_date[3];
    }
    
    function holidayAllDay($date_current_holiday, $id_current_user, $next_holiday) {
            $tab_jour = explode('_',$next_holiday->jour);
            $date_next_holiday = new DateTime($tab_jour[0].'-'.$tab_jour[1].'-'.$tab_jour[2]);
            return ($date_current_holiday->diff($date_next_holiday)->days == 0) && ($next_holiday->id_user == $id_current_user);
    }
    
    function codeIsCorrect($bdd)
    {
        $sql = $bdd->prepare("SELECT email FROM utilisateurs WHERE id= :id");
        $sql->execute(array(':id' => $_GET['id']));
        $res = $sql->fetch();
        $code = new Encryption([$_GET['id'], $res['email']]);
        return $_GET['code'] == $code->getTextHasher();
    }
?>