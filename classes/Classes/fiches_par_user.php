<?php

    //IMPORTANT :  Il ne faut PAS inclure la connexion mais la mettre comme ci-dessous car bug connu avec PHPEXCEL (fichier gÈnÈrÈ via le navigateur inexploitable !!!!)
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
    
    $arr_lettres = array(2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z',26=>'AA',27=>'AB',28=>'AC',29=>'AD',30=>'AE',31=>'AF',32=>'AG');
    
    function cellColor($cells,$color)
    {
        global $objPHPExcel;
        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()
        ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => $color)
        ));
    }
    
    
    $sql = "SELECT id, nom, prenom FROM utilisateurs";
    $fiches = $bdd->prepare($sql);
    $users = $fiches->execute();   
    $res = $fiches->fetchAll(PDO::FETCH_OBJ);
    
    
    include 'PHPExcel.php';
    include 'PHPExcel/Writer/Excel2007.php';
    
    $objPHPExcel = new PHPExcel();
    
    //$objPHPExcel->setActiveSheetIndex(0);
    
    //attention, ne pas mettre $i à 0 autrement bug dans phpexcel, décalage dans les titres des onglets.  Les fichiers excel commencent à 1 pas à 0.
    $i=1;
    foreach($res as $user)
    {
        
        $sheet = $objPHPExcel->createSheet();
        
        $objPHPExcel->setActiveSheetIndex($i);
        $sheet->setTitle($user->prenom.' '.$user->nom);
        //echo $sheet->getTitle();
        for($tour = 1; $tour <= 2; $tour++)
        {
            //je compte le nombre de DEMI-jours pris par la personne (je préfère compter en demi-jours)
            $sql = "SELECT jour FROM reservations ";
            $sql.= "WHERE id_user = ".$user->id;
            if($tour == 1)
            {
                $sql.= " AND SUBSTR(jour,1,4) = '".date('Y')."' ";   
            }
            else
            {
                $sql.= " AND SUBSTR(jour,1,4) = '".(date('Y')+1)."' ";
            }
            
            //echo $sql.'<br />';
            
            $req = $bdd->prepare($sql);
            $execution = $req->execute();
            $res = $req->fetchAll(PDO::FETCH_OBJ);
            
            //echo count($res).'<br />';
            if($tour == 1)
            {
                $objPHPExcel->getActiveSheet()->SetCellValue('D11','  NOMBRE DE JOURS DE CONGES PRIS : '.count($res)/2);    
            }
            else
            {
                $objPHPExcel->getActiveSheet()->SetCellValue('G33','  NOMBRE DE JOURS DE CONGES PRIS : '.count($res)/2);    
            }
            
            
            
            //j'affiche en Excel les jours de 1 à 31 (comme sur l'original de Mr Amighi)
            if($tour == 1)
            {
                $col = 'C';
                $ligne = 17;
                for($jour = 1; $jour <=31; $jour++)
                {
                    $objPHPExcel->getActiveSheet()->SetCellValue($col.'17', $jour);
                    $col++;
                }
                
                $ligne = 18;
                $arr_months = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
                //print_r($arr_months);
                for($mois = 1; $mois <= 12; $mois++)
                {
                   $objPHPExcel->getActiveSheet()->SetCellValue('A'.$ligne, $arr_months[$mois-1]);
                   
                   $ligne++;
                }
            }//fin de if($tour == 1)
            else
            {
                $col = 'C';
                $ligne = 37;
                for($jour = 1; $jour <=31; $jour++)
                {
                    $objPHPExcel->getActiveSheet()->SetCellValue($col.'37', $jour);
                    $col++;
                }
                $ligne = 38;
                $arr_months = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
                //print_r($arr_months);
                for($mois = 1; $mois <= 12; $mois++)
                {
                   $objPHPExcel->getActiveSheet()->SetCellValue('A'.$ligne, $arr_months[$mois-1]);
                   //echo 'A'.$ligne.' ' . $arr_months[$mois-1].' <br />';
                   $ligne++;
                }
            }
            
            
            $sql = "SELECT jour, commentaire
                     FROM reservations
                     WHERE id_user = ".$user->id;
            if($tour == 1)
            {
                $sql.= " AND SUBSTR(jour,1,4) = '".date('Y')."' ORDER BY jour";   
            }
            else
            {
                $sql.= " AND SUBSTR(jour,1,4) = '".(date('Y')+1)."' ORDER BY jour";
            }
            
             
             //echo $sql.'<br />';
             
             $req = $bdd->prepare($sql);
             $execution = $req->execute();
             $res = $req->fetchAll(PDO::FETCH_OBJ);
             //echo '<pre>';
             //print_r($res);
             //echo '</pre>';
             foreach($res as $periodes)
             {
                    $arr_periodes = explode('_',$periodes->jour);
                    //echo '[2] => '. $arr_periodes[2].'<br />';
                    //echo '[1] => '. $arr_periodes[1].'<br />';
                    //je démarre toujorus à B17, j'ajoute donc à B le nombre de jours pour être sur le bon jour et ensuite j'ajoute un nombre pour choisir le mois correct
                    //Choix du jour => $arr_periodes[2]+2 (2-1 pour la colonne B, colonne de départ) + $arr_periodes[2] qui est le jour
                    //Choix du mois => 17+($arr_periodes[1]) on commence à 17 et on ajoute le nombre qui correspond au mois retourné par la BD
                    
                    $sql = "SELECT jour
                          FROM reservations
                          WHERE id_user = ".$user->id;
                  $sql.= " AND SUBSTR(jour,1,10) = '". $arr_periodes[0]."_".$arr_periodes[1]."_".$arr_periodes[2]."' ORDER BY jour ";
                  $req2 = $bdd->prepare($sql);
                  $execution2 = $req2->execute();
                  $res2 = $req2->fetchAll(PDO::FETCH_OBJ);
                  $jour_entier_ou_demi = count($res2).'<br />'; //1 => demi-jour 2 => jour entier
                  
                  if($tour == 1)
                  {
                        
                        //je vérifie si c'est un demi-jour de congé
                        if($jour_entier_ou_demi == 1)
                        {
                           $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(($arr_periodes[2]+1),17+($arr_periodes[1]),'/');
                           
                           $coordonate = ($arr_periodes[2]+1).(17+$arr_periodes[1]);
                           //je mets une couleur de fond pour que l'on voit mieux les congés demandés
                           cellColor($arr_lettres[($arr_periodes[2]+1)].(17+$arr_periodes[1]), 'F28A8C');
                            
                           //ajout du commentaire
                           
                           //echo $cell.'<br />';
                           
                           
                           
                           //j'affiche le commentaire si un commentaire existe
                           $objPHPExcel->getActiveSheet()
                            ->getComment($arr_lettres[($arr_periodes[2]+1)].(17+$arr_periodes[1]))
                            ->getText()->createTextRun($periodes->commentaire);
                           
                                           
                        }
                        else
                        {
                           $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(($arr_periodes[2]+1),17+($arr_periodes[1]),'X');
                           //je mets une couleur de fond pour que l'on voit mieux les congés demandés
                           cellColor($arr_lettres[($arr_periodes[2]+1)].(17+$arr_periodes[1]), 'F28A8C');
                           
                           //j'affiche le commentaire si un commentaire existe
                           $objPHPExcel->getActiveSheet()
                            ->getComment($arr_lettres[($arr_periodes[2]+1)].(17+$arr_periodes[1]))
                            ->getText()->createTextRun($periodes->commentaire);
                        }
               
                  }//fin de if($tour == 1)  
                  else
                  {
                    
                        if($jour_entier_ou_demi == 1)
                        {
                           $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(($arr_periodes[2]+1),37+($arr_periodes[1]),'/');
                           //je mets une couleur de fond pour que l'on voit mieux les congés demandés
                           cellColor($arr_lettres[($arr_periodes[2]+1)].(37+$arr_periodes[1]), 'F28A8C');
                           
                           //j'affiche le commentaire si un commentaire existe
                           $objPHPExcel->getActiveSheet()
                            ->getComment($arr_lettres[($arr_periodes[2]+1)].(37+$arr_periodes[1]))
                            ->getText()->createTextRun($periodes->commentaire);
                                           
                        }
                        else
                        {
                           $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(($arr_periodes[2]+1),37+($arr_periodes[1]),'X');
                           //je mets une couleur de fond pour que l'on voit mieux les congés demandés
                           cellColor($arr_lettres[($arr_periodes[2]+1)].(37+$arr_periodes[1]), 'F28A8C');
                           
                           //j'affiche le commentaire si un commentaire existe
                           $objPHPExcel->getActiveSheet()
                            ->getComment($arr_lettres[($arr_periodes[2]+1)].(37+$arr_periodes[1]))
                            ->getText()->createTextRun($periodes->commentaire);
                        }
               
                    }  
                    
                }
                  
                  
                  $objPHPExcel->getActiveSheet()->SetCellValue('C32','   X   = 1 jour de congé  ');
                  $objPHPExcel->getActiveSheet()->SetCellValue('C33','/  = 1/2 journée');
                  if($tour == 1)
                  {
                        $objPHPExcel->getActiveSheet()->SetCellValue('K12','FICHE DE CONGES '.date('Y'));
                        $objPHPExcel->getActiveSheet()->SetCellValue('O12',$user->prenom.' '.$user->nom);
                        $styleArray = array(
                        'font'  => array(
                            'bold'  => true,
                            'color' => array('rgb' => 'FF0000'),
                            'size'  => 25,
                            'name'  => 'Verdana'
                        ));
                        $objPHPExcel->getActiveSheet()->getStyle('O12')->applyFromArray($styleArray);
                   }
                   else
                   {
                     $objPHPExcel->getActiveSheet()->SetCellValue('K33','FICHE DE CONGES '.(date('Y')+1));
                     $objPHPExcel->getActiveSheet()->SetCellValue('O33',$user->prenom.' '.$user->nom);
                     $styleArray = array(
                        'font'  => array(
                            'bold'  => true,
                            'color' => array('rgb' => 'FF0000'),
                            'size'  => 25,
                            'name'  => 'Verdana'
                        ));
                        $objPHPExcel->getActiveSheet()->getStyle('O33')->applyFromArray($styleArray);
                   }
                  $objPHPExcel->getActiveSheet()->SetCellValue('P14','Service : Pharmacie Galénique et biopharmacie');
                  
                  $objPHPExcel->getDefaultStyle()
                 ->applyFromArray(array(
                 'font'=>array(
                     'name'      =>  'Arial',
                     'size'      =>  12,
                     'bold'      => true),
                 'alignment'=>array(
                     'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                 'borders' => array(
                     'allborders'=>array(
                     'style' => PHPExcel_Style_Border::BORDER_DASHDOT))
                     )
                 );
                  
                  
                  //on centre le contenu
                  $objPHPExcel->getActiveSheet()->getStyle('A17:AG29')->applyFromArray(
                 array('alignment'=>array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))
                 );
                  
                  $objPHPExcel->getActiveSheet()->getStyle('A17:AG29')->getBorders()->getAllBorders()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK));
                  //on met des bordures
                  $styleArray = array(
                 'borders' => array(
                   'allborders' => array(
                     'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                   )
                 )
               );
                
                if($tour == 1)
                {
                    $objPHPExcel->getActiveSheet()->getStyle('A17:AG29')->applyFromArray($styleArray);
                    unset($styleArray);   
                }
                else
                {
                    $objPHPExcel->getActiveSheet()->getStyle('A37:AG49')->applyFromArray($styleArray);
                    unset($styleArray);   
                }
                 
        if($tour == 1)
        {
                    
           //j'ajoute le logo de la Faculté
           $objDrawing = new PHPExcel_Worksheet_Drawing();
           $objDrawing->setName('logo PHARMA');
           $objDrawing->setDescription('logo PHARMA');
           $objDrawing->setPath('../../images/pharma.png');
           $objDrawing->setHeight(136);
           $objDrawing->setCoordinates('B1');
           $objDrawing->setOffsetX(-10);
           $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
           
           //j'ajoute le logo de l'ULB
           $objDrawing = new PHPExcel_Worksheet_Drawing();
           $objDrawing->setName('logo ULB');
           $objDrawing->setDescription('logo ULB');
           $objDrawing->setPath('../../images/ulb.jpg');
           $objDrawing->setHeight(136);
           $objDrawing->setCoordinates('M1');
           $objDrawing->setOffsetX(-10);
           $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());        
        }         
                 
        }
        
       
        $i++;
        //echo $i;
    }//fin du foreach($res as $user)
    
    
 
   $writer = new PHPExcel_Writer_Excel2007($objPHPExcel);
   $writer->save(str_replace('.php', '.xlsx', __FILE__));
   
   echo 'Exportation OK, veuillez cliquer -> <a href="http://congespharma.ulb.ac.be/classes/Classes/fiches_par_user.xlsx"><img src="../../images/excel.png" /></a> pour ouvrir ou sauvegarder le document';
?> 