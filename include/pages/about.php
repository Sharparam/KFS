<?php
    $members = array(
        "Gunilla Andersson" => "Ordförande",
        "Helena Röjder" => "Vice Ordförande",
        "Carina Johansson" => "Sekreterare",
        "Lars Håkansson" => "Kassör",
        "Veronica Andersson" => "",
        "Britta Lundgren" => "",
        "Louise Hamilton" => ""
    );
?>

<h1 class="center_text">Om oss</h1>
<p class="center_text">Vi ingår i den rikstäckande organisationen &quot;Sveriges Förenade Filmstudios&quot; som har
    funnits i kommunen sedan 1956 och får ekonomiskt stöd av Kristinehamns kommun.</p>
<h3 class="center_text">Styrelsen för Kristinehamns Filmstudio</h3>
<table class="table table-condensed memberstable">
    <thead>
        <tr>
            <th>Namn</th>
            <th>Position</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($members as $name => $role): ?>
        <tr>
            <td><?php echo $name; ?></td>
            <td><?php echo $role; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<p class="center_text">Vill du veta mer om vår verksamhet,<br>
    kontakta ordförande Gunilla Andersson, tel. 073 074 47 11, <a href="mailto:055010135@telia.com">055010135@telia.com</a>;<br>
    eller kassör Lars Håkansson, tel. 0550 100 06.</p>
<p class="center_text">Vår centrala organisation heter Sveriges Förenade Filmstudios, SFF:<br>
    Verksamhetsansvarig: Per Eriksson, tel. 08 665 12 30, mob. 070 364 25 18;<br>
    Filmbokning: Tomas Tengmark, 08 66 51 31.<br>
    <a href="http://www.sff-filmstudios.org">Mer information finns på deras hemsida</a>.</p>
<p class="center_text" style="margin-top: 25px;">Sidan är gjord av Adam Hellberg (<a href="mailto:adam.hellberg@sharparam.com" title="Skicka frågor om sidan till mig!">adam.hellberg@sharparam.com</a>).
    Om du har frågor om webbsidan, skicka dem till mig!</p>
