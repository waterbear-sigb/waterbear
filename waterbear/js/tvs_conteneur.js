/**
CSS : le constructeur prend en paramètre 2 classes CSS
classe_elements => la classe appliquée à tous les éléments contenus dans le conteneur. Si on veut faire du cas par cas, il faut prévoir une méthode 
                    propre à chaque élément pour modifier sa classe CS
classe_conteneur => ne sera pas utilisé tel quel. A partir de cette chaine on forgera :
classe_conteneur+"_conteneur" => le conteneur global
classe_conteneur+"_menu" => le bandeau du menu
classe_conteneur+"_conteneur_elements" => le conteneur des éléments
Pour chaque classe, des éléments sont définis par l'attribut 'role'
MENU !!!
cellule_icone => a cellule (<td>) qui contient les icones
image_icone => l'image (<img>)
table_menu => la balise <table> qui contient tout
cellule_titre => la balise <td> qui contient le titre

Le lien entre les élémnts DOM et les éléments de liste_elements est assuré grâce à l'attribut 'name' des objets DOM qui correspond à l'id des élémnts de liste_elements

**/
function tvs_conteneur (element_parent, classe_conteneur, classe_elements) {
    
    // VARIABLES
    this.element_parent = element_parent; // L'élément où on va introduire le conteneur
    this.classe_elements=classe_elements;
    this.menu; // le menu (si défini)
    this.liste_elements=new Object();
    this.formulator=new Object(); // INFOS mises à jour par a méthode set_formulator() quand le conteneur est utilisé dans un formulator
    
    // CONSTRUCTEUR
    this.conteneur=document.createElement("div"); // la <div> conteneur
    this.conteneur.className = classe_conteneur+"_conteneur_elements";
    this.element_parent.appendChild(this.conteneur);
    this.conteneur.style.visibility="visible";
    
    // METHODES
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // cette méthode ajoute un élément au conteneur
    this.add_element = function (element, id) {
        element.className = this.classe_elements;
        element.setAttribute("name", id);
        this.conteneur.appendChild(element);
        this.liste_elements[id]=element;
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cette méthode déplace un élement dans le conteneur
    // Il déplace l'élément elem_a_bouger et le met AVANT l'élément destination
    this.move_element = function (elem_a_bouger, destination) {
        elem_a_bouger=this.liste_elements[elem_a_bouger];
        destination=this.liste_elements[destination];
        this.conteneur.insertBefore(elem_a_bouger, destination);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cette méthode supprime l'élément
    this.delete_element = function (element) {
        this.conteneur.removeChild(this.liste_elements[element]);
        delete this.liste_elements.element;
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cette méthode réorganise les éléments
    // Le nouvel ordre est fourni sous la forme d'une chaine de caractères ou les id des éléments sont fournis séparés par des virgules
    this.order_elements = function (str_ordre) {
        var ordre = str_ordre.split(",");
        var dernier_element="";
        for (var i = ordre.length - 1 ; i >= 0 ; i--) {
            var element=this.liste_elements[i];
            if (dernier_element=="") {
                dernier_element=null; // element bidon pour mettre à la fin
            }
            this.conteneur.insertBefore(element, dernier_element);
            dernier_element=element;
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cette méthode crée le menu
    // le paramètre 'classe_menu' est déprécié. La classe est générée à partir de la classe fournie au constructeur (en rajoutant "_menu")
    this.add_menu = function (titre, classe_menu, icones) {
        this.menu=document.createElement("div"); // la <div> conteneur
        this.menu.className = classe_conteneur+"_menu";
        var html_icones="";
        for (id in icones) {
            var src=icones[id]["src"];
            var alt=icones[id]["alt"];
            var action=icones[id]["action"];
            src=add_skin(src);
            action=this.formate_lien(action); // on remplace les motifs par les valeurs
            html_icones+="<td role='cellule_icone' class='"+classe_conteneur+"_menu"+"'><img class='"+classe_conteneur+"_menu"+"' role='image_icone' src='"+src+"' alt='"+alt+"' title='"+alt+"' onClick=\""+action+"\"/></td>";
        }
        this.menu.innerHTML="<table role='table_menu' class='"+classe_conteneur+"_menu"+"'><tr><td role='cellule_titre' class='"+classe_conteneur+"_menu"+"'>"+titre+"</td><td role='cellule_toutes_icones' class='"+classe_conteneur+"_menu"+"'><table><tr>"+html_icones+"</tr></table></tr></table>";
        this.element_parent.insertBefore(this.menu, this.conteneur);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cette méthode masque ou affiche le conteneur (sauf menu)
    this.hide_show_conteneur = function () {
        if (this.conteneur.style.visibility=="visible") {
            this.conteneur.style.visibility="hidden";
            this.conteneur.style.height="0px";
        } else {
            this.conteneur.style.visibility="visible";
            this.conteneur.style.height="";
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cette méthode masque complètement un conteneur (menu compris)
    this.hide_element = function () {
        this.element_parent.style.height="0px";
        this.element_parent.className="tvs_formulator_conteneur_champ_hidden";
        this.conteneur.style.visibility="hidden";
        //this.conteneur.style.display="none";
        this.menu.style.visibility="hidden";
        //this.menu.style.display="none";
        this.conteneur.style.height="0px";
        this.menu.style.height="0px";
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cette méthode affiche complètement un conteneur (menu compris)
    this.show_element = function () {
        this.element_parent.style.height="";
        this.element_parent.className="tvs_formulator_conteneur_champ";
        this.conteneur.style.visibility="visible";
        this.menu.style.visibility="visible";
        this.conteneur.style.height="";
        this.menu.style.height="";
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Met à jour this.formulator qui contient :
    // ["formulator"] : le nom de l'objet à formulator
    // ["element"] : l'ID de CE conteneur
    // ["conteneur"] : l'ID du conteneur parent
    this.set_formulator = function (formulator) {
        this.formulator = formulator;
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cette méthode permet de remplacer  les mots-clefs par des varaibles (dans un lien par ex)
    this.formate_lien = function (chaine) {
        var reg1 = new RegExp ("#formulator#", "gi");
        var reg2 = new RegExp ("#element#", "gi");
        var reg3 = new RegExp ("#conteneur#", "gi");
        chaine = chaine.replace(reg1, this.formulator["formulator"]);
        chaine = chaine.replace(reg2, this.formulator["element"]);
        chaine = chaine.replace(reg3, this.formulator["conteneur"]);
        return(chaine);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    
    this.monte_element = function (id) {
        try {
            var element2=this.liste_elements[id].previousSibling;
            var id2=element2.getAttribute("name");
            this.move_element(id, id2);
        } catch (e) {
            
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    
    this.descend_element = function (id) {
        try {
            var element2=this.liste_elements[id].nextSibling;
            var id2=element2.getAttribute("name");
            this.move_element(id2, id);
        } catch (e) {
            
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // retourne la liste des id dans l'ordre du DOM
    
    this.get_liste_elements_ordered = function () {
        var retour=new Array();
        var liste = this.conteneur.childNodes;
        for (idx in liste) {
            try {
                var id=liste[idx].getAttribute("name");
                retour.push(id);
            } catch (e) {
                
            }
        }
        return (retour);
    }
    

    
    
    
    
} // fin de la classe