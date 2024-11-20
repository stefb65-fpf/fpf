google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChartClubs);
google.charts.setOnLoadCallback(drawChartAdherents);
google.charts.setOnLoadCallback(drawChartRepartitionAdherents);
google.charts.setOnLoadCallback(drawChartEvolution);

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
                ['Pré-inscrits', reponse.preinscrits],
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
        colors: ['#e28a2d', '#f1e205', '#99d59a', '#0000FF'],
        // legend: { position: 'bottom' }
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
                ['Pré-inscrits', reponse.preinscrits],
                ['Renouvelés', reponse.valides],
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
        colors: ['#e28a2d', '#f1e205', '#99d59a', '#0000FF'],
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
                ['Adhérent 2nd club', reponse.ct6],
                ['Adhérent club famille', reponse.ct5],
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
    let data = new google.visualization.DataTable()
    let dataclubs = new google.visualization.DataTable()
    $.ajax({
        url:'/api/gestStatsEvolution',
        type: 'POST',
        data: {
            level: $('#levelStat').html(),
            ur_id: $('#urStat').html(),
        },
        dataType: 'JSON',
        success: function (reponse) {
            let tab_new = []
            Object.entries(reponse.adhesions[0]).forEach((element, index) => {
                tab_new[element[0]] = element[1]
            })

            let tab_prev = []
            Object.entries(reponse.adhesions[1]).forEach((element, index) => {
                tab_prev[element[0]] = element[1]
            })

            let tab_clubs_new = []
            Object.entries(reponse.clubs[0]).forEach((element, index) => {
                tab_clubs_new[element[0]] = element[1]
            })

            let tab_clubs_prev = []
            Object.entries(reponse.clubs[1]).forEach((element, index) => {
                tab_clubs_prev[element[0]] = element[1]
            })

            data = google.visualization.arrayToDataTable([
                ['Année', reponse.current_year, reponse.prev_year],
                ['09',  tab_new[9]??null,      tab_prev[9]??null],
                ['10',  tab_new[10]??null,      tab_prev[10]??null],
                ['11',  tab_new[11]??null,      tab_prev[11]??null],
                ['12',  tab_new[12]??null,      tab_prev[12]??null],
                ['01',  tab_new[1]??null,      tab_prev[1]??null],
                ['02',  tab_new[2]??null,      tab_prev[2]??null],
                ['03',  tab_new[3]??null,      tab_prev[3]??null],
                ['04',  tab_new[4]??null,      tab_prev[4]??null],
                ['05',  tab_new[5]??null,      tab_prev[5]??null],
                ['06',  tab_new[6]??null,      tab_prev[6]??null]
            ])

            dataclubs = google.visualization.arrayToDataTable([
                ['Année', 'Clubs ' + reponse.current_year, 'Clubs ' + reponse.prev_year],
                ['09',  tab_clubs_new[9]??null, tab_clubs_prev[9]??null],
                ['10',  tab_clubs_new[10]??null, tab_clubs_prev[10]??null],
                ['11',  tab_clubs_new[11]??null, tab_clubs_prev[11]??null],
                ['12',  tab_clubs_new[12]??null, tab_clubs_prev[12]??null],
                ['01',  tab_clubs_new[1]??null, tab_clubs_prev[1]??null],
                ['02',  tab_clubs_new[2]??null, tab_clubs_prev[2]??null],
                ['03',  tab_clubs_new[3]??null, tab_clubs_prev[3]??null],
                ['04',  tab_clubs_new[4]??null, tab_clubs_prev[4]??null],
                ['05',  tab_clubs_new[5]??null, tab_clubs_prev[5]??null],
                ['06',  tab_clubs_new[6]??null, tab_clubs_prev[6]??null]
            ])
        },
        async: false,
        error: function (e) {
        }
    })


    let options = {
        title: 'Evolution des adhésions',
        // curveType: 'function',
        legend: { position: 'bottom' },
        tooltip: { isHtml: true },
    };


    let chart = new google.visualization.LineChart(document.getElementById('linechartEvolution'));
    chart.draw(data, options);

    options.title = 'Evolution des clubs';
    let chartclub = new google.visualization.LineChart(document.getElementById('linechartEvolutionClub'));
    chartclub.draw(dataclubs, options);
}
