jQuery.sap.declare("bookLibraryList.Component");
sap.ui.core.UIComponent.extend("bookLibraryList.Component", {

	metadata : {
		rootView : "bookLibraryList.List",
		dependencies : {
			libs : [
				"sap.m",
				"sap.ui.layout"
			]
		},
		config : {
			sample : {
				files : [
					"List.view.xml",
					"List.controller.js"
				]
			}
		}
	}
});

jQuery.sap.declare("bookLibraryHome.Component");
sap.ui.core.UIComponent.extend("bookLibraryHome.Component", {
	metadata : {
		rootView : "bookLibraryHome.Home",
		dependencies : {
			libs : [
				"sap.m",
				"sap.ui.layout"
			]
		},
		config : {
			sample : {
				files : [
					"Home.view.xml",
					"Home.controller.js"
				]
			}
		}
	}
});

