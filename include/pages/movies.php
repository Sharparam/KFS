<h2 class="center_text">Filmer under <?php echo $SEASON_TEXT; ?></h2>
<p><em>Klicka på bilderna för att besöka respektive films IMDb-sida!</em></p>
<p>Där inget annat anges körs två visningar för varje film, en på eftermiddagen klockan 15:30 och ytterligare en på
    kvällen klockan 18:15.</p>
<div class="movie-list">
    <?php
    $query = "SELECT * FROM kfs_movies WHERE `show`=1 ORDER BY id ASC;";
    $stmt = $db->query($query);
    if ($stmt->rowCount() < 1) {
        ?>
        <h3><strong>Inga filmer just nu</strong></h3>
    <?php
    }
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        printMovie(
            $row->title,
            $row->orig_title,
            $row->genre,
            $row->country,
            $row->director,
            $row->length,
            $row->imdb,
            $row->image,
            $row->date,
            $row->year,
            $row->description
        );
    }
    ?>
</div>
<h2>Filmbetyg</h2>
<p>På filmvisningar har våra medlemmar möjlighet att rösta på filmer som visats, resultaten av betygsättningen visas här
    (betygskalan är 1 till 5 där 5 är bäst).</p>
<div class="row">
    <div class="col-md-4">
        <table class="table table-condensed ratings">
            <thead>
            <tr>
                <th>Film</th>
                <th>Betyg</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th class="ratingheader" colspan="2">HT 2014</th>
            </tr>
            <tr>
                <td>The Lunchbox</td>
                <td>4,0</td>
            </tr>
            <tr>
                <td>One Chance</td>
                <td>4,4</td>
            </tr>
            <tr>
                <td>Ilo Ilo</td>
                <td>3,2</td>
            </tr>
            <tr>
                <td>The Selfish Giant</td>
                <td>3,3</td>
            </tr>
            <tr>
                <td>Victor och Josefine</td>
                <td>3,4</td>
            </tr>
            <tr>
                <th class="ratingheader" colspan="2">VT 2015</th>
            </tr>
            <tr>
                <td>Nebraska</td>
                <td>4,2</td>
            </tr>
            <tr>
                <td>Sådan far, sådan son</td>
                <td>4,2</td>
            </tr>
            <tr>
                <td>Tålamodets sten</td>
                <td>4,3</td>
            </tr>
            <tr>
                <td>Philomena</td>
                <td>4,5</td>
            </tr>
            <tr>
                <td>Prästens barn</td>
                <td>3,2</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-4">
        <table class="table table-condensed ratings">
            <thead>
                <tr>
                    <th>Film</th>
                    <th>Betyg</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th class="ratingheader" colspan="2">HT 2015</th>
                </tr>
                <tr>
                    <td>Hemkomsten</td>
                    <td>4,2</td>
                </tr>
                <tr>
                    <td>Girlhood</td>
                    <td>2,7</td>
                </tr>
                <tr>
                    <td>Kvinnan i Guld</td>
                    <td>4,3</td>
                </tr>
                <tr>
                    <td>Innan frosten</td>
                    <td>3,6</td>
                </tr>
                <tr>
                    <td>Pride</td>
                    <td>4,3</td>
                </tr>
				<tr>
					<th class="ratingheader" colspan="2">VT 2016</th>
				</tr>
				<tr>
					<td>Varje gång jag ser dig</td>
					<td>3,5</td>
				</tr>
                <tr>
                    <td>Phoenix</td>
                    <td>4,0</td>
                </tr>
                <tr>
                    <td>Ida</td>
                    <td>3,5</td>
                </tr>
                <tr>
                    <td>Still Life</td>
                    <td>3,6</td>
                </tr>
                <tr>
                    <td>Familjen Bélier</td>
                    <td>4,4</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-4">
        <table class="table table-condensed ratings">
            <thead>
                <tr>
                    <th>Film</th>
                    <th>Betyg</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th class="ratingheader" colspan="2">HT 2016</th>
                </tr>
                <tr>
                    <td>The Danish Girl</td>
                    <td>4,0</td>
                </tr>
                <tr>
                    <td>Under Sanden</td>
                    <td>4,4</td>
                </tr>
                <tr>
                    <td>Bland Män och Får</td>
                    <td>3,3</td>
                </tr>
                <tr>
                    <td>Där Vindarna Möts</td>
                    <td>3,1</td>
                </tr>
                <tr>
                    <td>Idol</td>
                    <td>3,8</td>
                </tr>
                <tr>
                    <td>Systrar</td>
                    <td>3,8</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
