<?php
        $kapcs=mysqli_connect("localhost","root","","faloda");
        mysqli_set_charset($kapcs,"utf8");
        mysqli_select_db($kapcs,"faloda");
        
        $muvelet="";
        $kezdolap=false;
        $lista=false;
        $uj_recept=false;
        $akt_recept=false;
        $rogzitve=false;
        $tajekoztato="";
        $rendezes="";
        $nev_rend="";
        $ido_rend="";
        $ar_rend="";
        $nincs_rend="";
        $keresett="";
        $feltetel="";

        if (!isset($_GET['recept']) && !isset($_GET['muvelet']) && !isset($_POST['rogzit'])) {
            $kezdolap=true;
            $lekerdezes="select * from receptek order by rand() limit 1";
            $eredmeny=mysqli_query($kapcs,$lekerdezes);
            $recept=mysqli_fetch_all($eredmeny,MYSQLI_ASSOC);
            $eredm_halmaz=mysqli_query($kapcs,
                "select ar, count(ssz) as db from receptek group by ar");
            $ar_dbk=mysqli_fetch_all($eredm_halmaz,MYSQLI_ASSOC);
        } elseif (isset($_GET['recept'])) {
            $akt_recept=true;
            $recept_nev=$_GET['recept'];
            $lekerdezes="select * from receptek where etel_url='$recept_nev'";
            $eredmeny=mysqli_query($kapcs,$lekerdezes);
            $recept=mysqli_fetch_all($eredmeny,MYSQLI_ASSOC);
        }elseif (isset($_GET['muvelet'])) {
            $muvelet=$_GET['muvelet'];
            if ($muvelet=="lista") {
                $lista=true;
                if (isset($_GET['keresett'])) {
                    $keresett=$_GET['keresett'];
                    $feltetel="where etel like '%$keresett%' or leiras like '%$keresett%'";
                }
                if (isset($_GET['rendez'])) {
                    $rendez=$_GET['rendez'];
                    if (empty($rendez)) {
                        $nincs_rend=" selected";
                    }else {
                        switch ($rendez) {
                            case 'nev':
                                $rendezes="order by etel";
                                $nev_rend=" selected";
                                break;
                            case 'ido':
                                $rendezes="order by elkeszites";
                                $ido_rend=" selected";
                                break;
                            case 'ar':
                                $rendezes="order by ar";
                                $ar_rend=" selected";
                                break;
                        }
                    }
                }else {
                    $nincs_rend=' selected';
                }
                $lekerdezes="select * from receptek $feltetel $rendezes";
                $eredmeny=mysqli_query($kapcs,$lekerdezes);
                $receptek=mysqli_fetch_all($eredmeny,MYSQLI_ASSOC);
                $db=mysqli_num_rows(mysqli_query($kapcs, "select * from receptek $feltetel"));
            }elseif ($muvelet=="uj_recept") {
                $uj_recept=true;
            }
        }elseif (isset($_POST['rogzit'])) {
            $rogzitve=true;
            $ssz=$_POST['ssz'];
            $etel=$_POST['etel'];
            $etel_url=$_POST['etel_url'];
            $elkeszites=$_POST['elkeszites'];
            $ar=$_POST['ar'];
            $leiras=$_POST['leiras'];
            $foto=$_FILES['foto']['name'];
            $hely="kepek/".basename($foto);
            if (!move_uploaded_file($_FILES['foto']['tmp_name'],$hely)) {
                print "Nem sikerült feltölteni az étel fotóját!";
            }
            $beszuras="insert into receptek
                        (ssz,etel_url,elkeszites,ar,foto,leiras)
                        vallues
                        (null,'$etel','$etel_url','$elkeszites','$ar','$foto','$leiras')";
            if (mysqli_query($kapcs,$beszuras)) {
                $tajekoztato="A recept sikeresen rögzítve!";
            }else {
                echo "Hiba: <br>". mysqli_error($kapcs);
            }
        }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faloda étterem receptjei</title>
    <link rel="stylesheet" href="faloda.css">
    <script src="faloda.js"></script>
    <?php if ($kezdolap): ?>
        <script src="https://www.gstatic.com/charts/loader.js"></script>
        <script>
        google.charts.load('current',{'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            let data=google.visualization.arrayToDataTable([
                ['Ár','Darab'],
                <?php foreach ($ar_dbk as $ar_kateg):?>
                ['<?php echo $ar_kateg['ar']?>',<?php echo $ar_kateg['db']?>],
                <?php endforeach?>
            ]);
            let options={
                title: 'Az ételek áraránya',
                width: 800,
                heigth: 500,
                backgroundColor:{fill:'transparent'}
            };
            let chart=new google.visualization.PieChart(document.getElementById('kordiagram'))

            chart.draw(data,options);
        }
        </script>
        <?php endif?>
</head>
<body>
    <video autoplay muted loop>
        <source src="Nigella-falatozoja.mp4"
                type="video/mp4">
    </video>
    <main>
        <nav>
            <label for="keresett">Kereses: <input type="search" name="keresett" id="keresett">
            <button onclick="kereses(this)">Keres</button>
        </label>
        <?php if($lista):?>
            <label for="rendez">
                Rendezés: 
                <select name="rendez" id="rendez" onchange="rendez(this)">
                <option value="" <?php echo $nincs_rend?>></option>
                <option value=nev <?php echo $nev_rend?>>név</option>
                <option value=ido <?php echo $ido_rend?>>idő</option>
                <option value=ar <?php echo $ar_rend?>>ár</option>
            </select>
            </label>
            <?php endif?>
            <?php if(!$kezdolap):?>
                <a href="?">Kezdőlap</a>
            <?php endif?>
            <?php if(!$lista):?>
                <a href="?muvelet=lista">Lista</a>
            <?php endif?>
            <?php if (!$uj_recept):?>
                <a href="?muvelet=uj_recept">Új recept</a>
            <?php endif?>
        </nav>
        
        <?php if ($kezdolap): ?>
            <h1>Üdvözöllek a Faloda étterem weboldalán</h1>
        <?php elseif ($akt_recept): ?>
            <h1><?php echo ucfirst($recept[0]['etel'])?> </h1>
        <?php elseif ($lista): ?>
            <?php if (empty($keresett)): ?>
            <h1>Teljes lista a receptekről</h1>
        <?php else: ?>
            <h1><?php echo $db?> db receptben fordul elő a(z) '<?php echo $keresett?>' szó</h1>
        <?php endif?>
        <?php elseif ($uj_recept):?>
            <h1>Új recept felvétele</h1>
        <?php elseif ($rogzitve):?>
            <h1><?php echo $tajekoztato?></h1>
        <?php endif?>
        <?php if ($kezdolap || $akt_recept):?>
            <section>
                <?php if($kezdolap):?>
                    <h2><a href="?recept=<?php echo $recept[0]['etel_url']?> ">
                    <?php echo ucfirst($recept[0]['etel'])?>
                </a></h2>
                <?php endif?>
                <figure>
                    <img src="kepek/<?php echo $recept[0]['foto']?> ">
                    <figcaption>
                        <span>idő: <?php echo $recept[0]['elkeszites']?> perc</span>
                        <span>ár: <?php echo $recept[0]['ar']?></span>
                    </figcaption>
                </figure>
                <article>
                    <?php echo $recept[0]['leiras']?>
                </article>
                <?php if($akt_recept):?>
                    <aside>
                        <b>Hozzávalók</b>
                        <menu>
                            
                            <li>Lorem, ipsum dolor.</li>
                            <li>Unde, reiciendis quae?</li>
                            <li>Similique, voluptatibus voluptatem.</li>
                            <li>Laboriosam, consequuntur labore?</li>
                            <li>Dolorum, atque minima!</li>
                            
                        </menu>
                    </aside>
                    <?php endif?>
            </section>
            <?php if($kezdolap):?>
                <div id="kordiagram"></div>
            <?php endif?>
            <?php elseif($lista):?>
                <?php foreach ($receptek as $recept): ?>
                    <section>
                        <h2>
                            <a href="?recept=<?php echo $recept['etel_url'] ?>">
                            <?php echo ucfirst($recept['etel'])?>
                        </a>
                        </h2>
                        <figure>
                            <img src="kepek/<?php echo $recept['foto']?>">
                            <figcaption>
                                <span>idő: <?php echo $recept['elkeszites']?> perc</span>
                                <span>ár: <?php echo $recept['ar']?> </span>
                            </figcaption>
                        </figure>
                        <article>
                            <?php echo $recept['leiras']?>
                        </article>
                    </section>
                <?php endforeach?>
            <?php elseif ($uj_recept):?>
                <form name="recept" action="<?php echo basename(__FILE__)?>" method=post enctype="multipart/form-data">
            <fieldset>
                <legend>Recept</legend>
                <input type="hidden" name=ssz value=null>
                <p>
                    <label for="etel">Étel:</label>
                    <input type="text" name=etel size=50 maxlenght=50 required onblur="ekezetlenit()">
                </p>
                <input type="hidden" name=etel_url>
                <p>
                    <label for="elkeszites">Elkészítés: </label>
                    <input type="number" name=elkeszites min=1 max=999 required>
                </p>
                <p>
                    <label for="ar">Ár:</label>
                    <select name="ar" id="ar" required>
                        <option></option>
                        <option>olcsó</option>
                        <option>átlagos</option>
                        <option>megfizethető</option>
                        <option>költséges</option>
                    </select>
                </p>
                <p>
                    <label for="foto">Fotó: </label>
                    <input type="file" name="foto" id="foto" required>
                </p>
                <p>
                    <label for="leiras">Leírás: </label><br>
                    <textarea name="leiras" id="leiras" cols="60" rows="10" required></textarea>
                </p>
                <nav>
                    <input type="submit" name=rogzit value="Rögzítés">
                    <input type="reset" name=visszaallit value="Visszaállítás">
                </nav>
            </fieldset></form>
            <?php endif?>
            <?php mysqli_close($kapcs)?>
    </main>
    

</body>
</html>