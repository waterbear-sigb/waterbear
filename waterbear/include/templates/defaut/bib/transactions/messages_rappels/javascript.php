<script language="javascript">

var ws_panier="<?PHP print($GLOBALS["affiche_page"]["parametres"]["ws_panier"]); ?>";

function init() {
    init_autocomplete();
}

function init_autocomplete(){

    oDS = new YAHOO.util.XHRDataSource(ws_panier);
    oDS.responseType = YAHOO.util.DataSourceBase.TYPE_JSARRAY;
    oDS.responseSchema = {fields : ["nom", "id"]}; // attention BUG YUI : les données ne seront pas accessibles via ["xxx"] mais [0], [1]...
    oDS.maxCacheEntries = 5;
    oAC = new YAHOO.widget.AutoComplete("input_panier", "autocomplete_input_panier", oDS);
    oAC.queryQuestionMark=false;

    
}

</script>