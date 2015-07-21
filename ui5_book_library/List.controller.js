sap.ui.controller("bookLibraryList.List", {

	onInit: function () {
		var oXMLModel = new sap.ui.model.xml.XMLModel();
		//oXMLModel.loadData('/mockdata/books_real.xml', '', false);
    oXMLModel.setXML(libraryXML); //libraryXML is set in index.html in the Login routine
    
		var htmlText = '<table id="IdBookList">';
		htmlText += '<thead><tr><th><span>Author</span></th><th><span>Title</span></th><th><span>Genre</span></th><th><span></span></th></tr></thead>';
		htmlText += '<tbody>'
		try {
		  var books = oXMLModel.oData.firstChild.childNodes;
	   	for (i = 0; i < books.length; i++) { 
	   		htmlText += '<tr>';
	      htmlText += '<td>' + books.item(i).attributes.getNamedItem('author').value + '</td>';
	     // htmlText += '"Author_sort": "' + books.item(i).attributes.getNamedItem('author_sort').value + '",';
	      htmlText += '<td>' + books.item(i).attributes.getNamedItem('title').value + '</td>';
	      htmlText += '<td>' + books.item(i).attributes.getNamedItem('genre').value + '</td>';
	      htmlText += '<td>' + '<a href="#" onclick="downloadBook(\'' + books.item(i).attributes.getNamedItem('filename').value + '\')">';
	      htmlText += '<img src="http://example.com/Download.png" width="20">' + '</a>' + '</td>';
	      htmlText += '</tr>';
	    }
	  } catch(err) {

	  }
    htmlText += '</tbody></table>';

		// // var htmlText = JSON.stringify(xmlToJson(oXMLModel.getXML()));
	 //  var oJSONModel = new sap.ui.model.json.JSONModel();
	 //  //oJSONModel.setSizeLimit(books.length); <- Um alle Zeilen zu sehen wird das gebraucht!
	 //  oJSONModel.setJSON(htmlText);
	 //  var oTable = this.getView().byId('IdBookList')
	 //  oTable.setModel(oJSONModel);
	 //  oTable.setFixedLayout(false);
		// test = htmlText;
		var oCoreHTML = this.getView().byId('IdHTMLContent');
		oCoreHTML.setContent(htmlText);
	},

	onAfterRendering: function() {
		$( "#IdBookList").css("table-layout", "auto");
		$( "#IdBookList" ).addClass( "sapMListModeNone sapMListShowSeparatorsAll sapMListTbl sapMListUl" );
		$( "#IdBookList thead tr" ).addClass("sapMListModeNone sapMListShowSeparatorsAll sapMListTbl sapMListUl");
		$( "#IdBookList thead tr" ).find( "th" ).addClass("sapMListTblCell sapMListTblHeaderCell");
		$( "#IdBookList thead" ).find( "span" ).addClass("sapMText sapMTextBreakWord sapMTextMaxWidth sapUiSelectable");
		$( "#IdBookList tbody" ).find( "tr" ).addClass("sapMLIB sapMLIB-CTX sapMLIBShowSeparator sapMLIBTypeInactive sapMListTblRow");
		$( "#IdBookList" ).find( "td" ).addClass("sapMListTblCell");
	}
});
