<?PHP
$no_notice=$_SESSION["operations"][$GLOBALS["affiche_page"]["parametres"]["ID_operation"]]["no_notice"];
?>

<!-- DIV ETAT  --------------------------------------------------------------------------------------------------------------------->
<div class="div_etat">
    <div class="div_recap" id="div_recap">
        
    </div>
</div>
<!-- DIVS NOTICES  ----------------------------------------------------------------------------------------------------------------->
<!-- Notice du fichier  ---------------------------------------------------->

<div class="div_notice" style="left: 0px; ">
    <div class="div_notice_txt" id="div_notice_txt_fichier">
        
    </div>
    <div class="div_notice_txt" id="div_notice_txt_url">
        
    </div>
</div>


<!-- DIV ACTIONS  ------------------------------------------------------------------------------------------------------------------>
<div class="div_action">
<input type="button" value="Notice suivante" onclick="get_notice(1)" />
</div>


