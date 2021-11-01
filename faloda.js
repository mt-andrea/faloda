function keres(elem) {
	document.location.href='?muvelet=lista&keresett='+elem.previousElementSibling.value;
}

function rendez(elem) {
	var kerKarLanc=document.location.search;
	var ertek=elem.value;
	if (kerKarLanc.indexOf("rendez") == -1)
	  document.location.href=kerKarLanc+'&rendez='+ertek;
	else {
	  if (ertek === "") {
		kerKarLanc=paramTorol("rendez",kerKarLanc);
		if (kerKarLanc.indexOf("sorrend") != -1)
		  kerKarLanc=paramTorol("sorrend",kerKarLanc);
		document.location.href=kerKarLanc;
		} 
		else
		 document.location.href=paramErtCsere("rendez",ertek);
	}
}

function sorrend(elem) {
	var kerKarLanc=document.location.search;
	var ertek=elem.value;
	if (kerKarLanc.indexOf("sorrend") == -1)
	  document.location.href=kerKarLanc+'&sorrend='+ertek;
	else 
	  document.location.href=paramErtCsere("sorrend",ertek);
}

function lapszamoz(objektum,esemeny) {
	if (esemeny.keyCode == 13) {
	  var kerKarLanc=document.location.search;
	  var ertek=objektum.value;
	  if (kerKarLanc.indexOf("lapdb") == -1)
		document.location.href=kerKarLanc+'&lapdb='+ertek;
	  else {
		kerKarLanc=paramErtCsere("lapdb",ertek);
		if (kerKarLanc.indexOf("oldal") != -1)
		  kerKarLanc=paramTorol("oldal",kerKarLanc);
		document.location.href=kerKarLanc;
	  }
	}
}

function oldalra(lapsorszam) {
	var kerKarLanc=document.location.search;
	if (kerKarLanc.indexOf("oldal") == -1)
	  document.location.href=kerKarLanc+'&oldal='+lapsorszam;
	else 
	  document.location.href=paramErtCsere("oldal",lapsorszam);
}

function paramErtCsere(parameter, ertek) {
  var kerKarLanc=document.location.search;
  var regularisKif = new RegExp("[\\?&]" + parameter + "=([^&#]*)"),
      elvalaszto = regularisKif.exec(kerKarLanc)[0].charAt(0),
      ujKerKarLanc = kerKarLanc.replace(regularisKif, elvalaszto + parameter + "=" + ertek);
  return ujKerKarLanc;
}

function paramTorol(parameter,kerKarLanc) {
  var regularisKif = new RegExp("[\\?&]" + parameter + "=([^&#]*)"),
      elvalaszto = regularisKif.exec(kerKarLanc)[0].charAt(0),
      ujKerKarLanc = kerKarLanc.replace(regularisKif, "");
  return ujKerKarLanc;
}

function ekezetlenit() {
	document.recept.etel_url.value=ekezetlenites(document.recept.etel.value);
}

function ekezetlenites(karakterlanc) {
  var bemenet = 'ÁáÓÖŐóöőÉéÍíÚÜŰúüű'.split('');
  var kimenet = 'AaOOOoooEeIiUUUuuu'.split('');
  var map = {};
  bemenet.forEach((i, idx) => map[i] = kimenet[idx]);
  return karakterlanc.replace(/[^A-Za-z0-9]/g, function(kar) { return map[kar] || kar; })
}


function betolt(lekerd_string) {
  var adat_manip_tip = (lekerd_string.indexOf("modosit") != -1) ? "módosítani" : 
					   (lekerd_string.indexOf("torol") != -1) ? "törölni" : "";
  var ssz = prompt("Melyik sorszámú receptet szeretnéd " + adat_manip_tip + "?");
  document.location.href=lekerd_string + "&ssz=" + ssz;
}