function tvs_conteneur (element_parent) {
    
    // VARIABLES
    this.conteneur=element_parent; // <DIV> qui contiendra le reste
    this.liste_objets = new Array(); // liste des objets contenus dans le conteneur
    this.largeur_conteneur; // largeur du conteneur
    this.couleur_conteneur; // couleur du conteneur
    this.marge_conteneur; // marge du conteneur
    this.hauteur = 0; // hauteur actuelle du conteneur (augmente au fur et à mesure qu'on rajoute des éléments)
    this.compteur = 0; // identifiant unique des éléments
    
    // METHODES
    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.init_conteneur = function (largeur, couleur, marge) {
        this.largeur_conteneur=largeur;
        this.couleur_conteneur=couleur;
        this.marge_conteneur=parseInt(marge);
        this.hauteur=this.marge_conteneur;
        
        this.conteneur.style.width=this.largeur_conteneur+"px";
        this.conteneur.style.height=this.hauteur+"px";
        this.conteneur.style.position="relative"; // ??
        this.conteneur.style.backgroundColor=this.couleur_conteneur;
        this.conteneur.style.visibility="visible";
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ajoute un élément à la fin
    this.add_element = function (elem, hauteur) {
        var tmp = new Array();
        tmp["elem"]=elem;
        tmp["hauteur"]=hauteur;
        tmp["visibility"]="visible";
        tmp["compteur"]=this.compteur; // TMP : le n° unique devra être attribué via Ajax
        this.compteur++;
        tmp["div"]=document.createElement("div");
        tmp["div"].appendChild(elem);
        tmp["div"].style.position="absolute";
        tmp["div"].style.top=this.hauteur+"px"; // on place l'élément à la fin
        tmp["div"].style.height=hauteur+"px";
        tmp["div"].style.width=this.largeur_conteneur-(this.marge_conteneur * 2)+"px";
        tmp["div"].style.left=this.marge_conteneur+"px";
        tmp["div"].style.backgroundColor="blue";
        tmp["div"].visibility="visible";
        
        this.hauteur+=parseInt(hauteur) + this.marge_conteneur; 
        this.conteneur.style.height=this.hauteur+"px";
        this.conteneur.appendChild(tmp["div"]);
        this.liste_objets.push(tmp);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    // intervertit 2 éléments
    this.switch_elements = function (elem1, elem2) {
        var retour=new Array();
        for (var i=0 ; i < this.liste_objets.length ; i++) {
            if (i == parseInt(elem1)) {
                retour[i]=this.liste_objets[elem2];
            } else if (i == parseInt(elem2)) {
                retour[i]=this.liste_objets[elem1];
            } else {
                retour[i]=this.liste_objets[i];
            }
        }
        this.liste_objets=retour;
        this.reload();
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    // raffraichit l'affichage
    this.reload = function () {
        this.hauteur=this.marge_conteneur;
        for (var i=0 ; i < this.liste_objets.length ; i++) {
            if (this.liste_objets[i]["visibility"] == "visible") {
                this.liste_objets[i]["div"].style.height=this.liste_objets[i].hauteur+"px";
                this.liste_objets[i]["div"].style.top=this.hauteur+"px";
                this.liste_objets[i]["div"].style.visibility="visible";
                this.hauteur=parseInt(this.hauteur)+parseInt(this.liste_objets[i]["hauteur"])+parseInt(this.marge_conteneur);
            } else {
                this.liste_objets[i]["div"].style.visibility="hidden";
            }   
        }
        this.conteneur.style.height=this.hauteur+"px";
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cache un élément
    this.hide = function (id) {
        this.liste_objets[id].visibility="hidden";
        this.reload();
        
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    // affiche un élément
    this.show = function (id) {
        this.liste_objets[id].visibility="visible";
        this.reload();
        
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    // affiche un élément
    this.change_hauteur_element = function (id, hauteur) {
        this.liste_objets[id].hauteur=parseInt(hauteur);
        this.reload();
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    // supprime un élément
    this.delete_element = function (id) {
        this.conteneur.removeChild(this.liste_objets[id]["div"]);
        var retour=new Array();
        for (var i=0 ; i < this.liste_objets.length ; i++) {
            if (i != parseInt(id)) {
                retour.push(this.liste_objets[i]);
            } 
        }
        this.liste_objets=retour;
        this.reload();
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    // récupère l'ID dans le tableau à partir du n° de compteur (n° unique)
    this.get_id_by_compteur = function (compteur) {
        for (var i=0 ; i < this.liste_objets.length ; i++) {
            if (parseInt(liste_objets[i]["compteur"]) == parseInt(compteur)) {
                return (i);
            }
        }
        return (-1);
    }
    
    
    
    
    
}