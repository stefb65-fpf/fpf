google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChartClubs);
google.charts.setOnLoadCallback(drawChartAdherents);
google.charts.setOnLoadCallback(drawChartRepartitionAdherents);
// google.charts.setOnLoadCallback(drawChartEvolution);

function drawChartClubs() {
    let data
    $.ajax({
        url:'/api/gestStatsClub',
        type: 'POST',
        data: {
            level: $('#levelStat').html(),
            ur_id: $('#urStat').html(),
        },
        dataType: 'JSON',
        success: function (reponse) {
            data = google.visualization.arrayToDataTable([
                ['Statut', 'Nombre de clubs'],
                ['Non renouvelés', reponse.non_renouveles],
                ['Renouvelés', reponse.valides],
                ['Nouveaux', reponse.nouveaux]
            ])
        },
        async: false,
        error: function (e) {
        }
    })

    let options = {
        title: 'Clubs',
        is3D: true,
        legend: { position: 'bottom' }
    }

    let chart = new google.visualization.PieChart(document.getElementById('piechartClubs'))
    chart.draw(data, options)
}

function drawChartAdherents() {
    let data
    $.ajax({
        url:'/api/gestStatsAdherents',
        type: 'POST',
        data: {
            level: $('#levelStat').html(),
            ur_id: $('#urStat').html(),
        },
        dataType: 'JSON',
        success: function (reponse) {
            data = google.visualization.arrayToDataTable([
                ['Statut', 'Nombre adhérents'],
                ['Non renouvelés', reponse.non_renouveles],
                ['Renouvelés', reponse.valides],
                ['Pré-inscrits', reponse.preinscrits],
                ['Nouveaux', reponse.nouveaux],
            ])
        },
        async: false,
        error: function (e) {
        }
    })

    let options = {
        title: 'Adhérents',
        is3D: true,
    }


    let chart = new google.visualization.PieChart(document.getElementById('piechartAdherents'))
    chart.draw(data, options)
}

function drawChartRepartitionAdherents() {
    let data
    $.ajax({
        url:'/api/gestStatsRepartitionCartes',
        type: 'POST',
        data: {
            level: $('#levelStat').html(),
            ur_id: $('#urStat').html(),
        },
        dataType: 'JSON',
        success: function (reponse) {
            data = google.visualization.arrayToDataTable([
                ['Type', 'Nombre adhérents'],
                ['Adhérent club > 25 ans', reponse.ct2],
                ['Adhérent club 18-25 ans', reponse.ct3],
                ['Adhérent club < 18 ans', reponse.ct4],
                ['Adhérent 2nd club', reponse.ct5],
                ['Adhérent club famille', reponse.ct6],
                ['Individuel > 25 ans', reponse.ct7],
                ['Individuel 18-25 ans', reponse.ct8],
                ['Individuel < 18 ans', reponse.ct9],
                ['Individuel famille', reponse.ctf],
            ])
        },
        async: false,
        error: function (e) {
        }
    })

    let options = {
        title: 'Répartition adhérents',
        is3D: true,
    }

    let chart = new google.visualization.PieChart(document.getElementById('piechartRepartitionAdherents'))
    chart.draw(data, options)
}

function drawChartEvolution() {
    let data = google.visualization.arrayToDataTable([
        ['Mois', 'Adhésions'],
        ['Septembre',  3659],
        ['Octobre',  6523],
        ['Novembre',  7859],
        ['Décembre',  7963]
    ]);

    let options = {
        title: 'Evolution des adhésions',
        // curveType: 'function',
        legend: { position: 'bottom' }
    };

    let chart = new google.visualization.LineChart(document.getElementById('linechartEvolution'));
    chart.draw(data, options);
}
