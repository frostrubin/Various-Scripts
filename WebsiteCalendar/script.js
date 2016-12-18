var Page = {
    ics: '',
    months: ['Jan.', 'Feb.', 'März', 'April', 'Mai', 'Juni', 'Juli', 'Aug.', 'Sep.', 'Okt.', 'Nov.', 'Dez.'],
    weekdays: ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'],

    onLoad: function() {
    	this.getICSFileAndBuildAgenda();
    },
    getICSFileAndBuildAgenda: function() {
        var oReq = new XMLHttpRequest();
        oReq.onload = function(e) {
         	Page.ics = oReq.response.toString();
         	Page._buildAgenda();
        }
        oReq.open("GET", './mock/basic.ics');
        oReq.send();
    },
    _buildAgenda: function() {
    	if (this.ics == '') {
    		return;
    	}

        var d = new Date();
        var todaysDate = d.getFullYear() + pad(( d.getMonth() + 1 ),2) + pad(d.getDate(),2);
    	var agenda = document.getElementById('agenda');
    	var newHTML = '';

    	var jcalData = ICAL.parse(this.ics);
    	var vcalendar = new ICAL.Component(jcalData);
    	var vevents = vcalendar.getAllSubcomponents('vevent');
    	vevents.sort();
    	vevents.forEach(function(vevent) {
    		var newEvent = new ICAL.Event(vevent);

    		var startDay = newEvent.startDate.day;
    		var startDayWeekday = newEvent.startDate.dayOfWeek();
    		console.log(startDay + startDayWeekday);
    		var startMonth = newEvent.startDate.month;
    		var startMonthText = Page.months[startMonth - 1];

            var endDay = newEvent.endDate.day;
            var endMonth = newEvent.endDate.month;
            var endMonthText = Page.months[endMonth - 1];

            var eventDate = newEvent.endDate.year + pad(newEvent.endDate.month,2) + pad(newEvent.endDate.day,2);
            if (eventDate < todaysDate) {
                return;
            }

    		newHTML += '<div class="column col-12">';
			newHTML += '<div class="toast toast-primary">';
			newHTML += '<table><tr><td>';

            
			newHTML += '<div class="mono">';
            if (newEvent.duration.days != 1) {
                if (startMonth == endMonth) {
                    if (startDay == endDay) {
                        // Duration is not 1 day, but same month and same day? 
                        // Then it is an Event with a time (eg. 17:00 to 20:00)
                        // But we don't care about the time ...
                        newHTML += pad(startDay,2) + '. ' + startMonthText;
                    } else {
                        newHTML += pad(startDay,2) + '. - ' + pad(endDay,2) + '. ' + startMonthText;
                    }
                } else {
                    newHTML += pad(startDay,2) + '. ' + startMonthText + ' - ' + pad(endDay,2) + '. ' + endMonthText;
                }
            } else {
                newHTML += pad(startDay,2) + '. ' + startMonthText;
            }
            newHTML += '</div>';
			newHTML += '</td><td>'
			newHTML += '<div class="small">' + Page.weekdays[startDayWeekday - 1] + '</div>';
			newHTML += '</td></tr><tr><td colspan="2">';
			newHTML += vevent.getFirstPropertyValue('summary');


			newHTML += '</td></tr></table>';
			newHTML += '</div>';
			newHTML += '</div>';
    	});

		agenda.innerHTML = newHTML;
    },
}



function pad(n, width, z) {
    z = z || '0';
    n = n + '';
    return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

