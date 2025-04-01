import axios from 'axios';
import Chart from 'chart.js/auto';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import 'datatables.net-responsive-dt';
import 'datatables.net-buttons-dt';
import flatpickr from "flatpickr";

var nombresEmpresas = ["Empresa 1", "Empresa 2", "Empresa 3", "Empresa 4", "Empresa 5", "Empresa 6", "Empresa 7", "Empresa 8", "Empresa 9", "Empresa 10"];
var ingresos = [780, 900, 150, 893, 400, 180, 220, 280, 100, 320];
var egresos = [80, 150, 120, 200, 180, 90, 160, 210, 140, 230];



const canvas  = document.getElementById('ingresoxempresa');
const canvas2  = document.getElementById('egresoxempresa');

if(canvas!=null && canvas2!==null){
const ctx = canvas.getContext("2d");
const ctx2=canvas2.getContext("2d");

$(document).ready(function() {
    const userTheme = localStorage.getItem('theme');
    const bgGradient = $('.bg-gradient-dark');
    const bgGradient2 = $('.bg-gradient-light');
    const btnContrast = $('#btnContrast');

    if (userTheme === 'dark') {
        bgGradient2.removeClass('bg-gradient-light').addClass('bg-gradient-dark');
        btnContrast.html('<i class="ri-contrast-2-fill"></i>');
    } else {
        bgGradient.removeClass('bg-gradient-dark').addClass('bg-gradient-light');
        btnContrast.html('<i class="ri-sun-line"></i>');
    }
});


$.get('/ingresosxempresas', function(dt){
    console.log(dt);
    var labels = dt.map(item => item.nombre); // Extrae los labels
    var data = dt.map(item => item.cant); // Extrae los datos
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Ingresos por Empresas',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.5)', // Color de fondo de las barras
                borderColor: 'rgba(54, 162, 235, 1)', // Color del borde de las barras
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true // Empieza el eje y desde cero
                }
            }
        }
    });
})

$.get('/egresosxempresas',function(dt){

    var labels = dt.map(item => item.nombre); // Extrae los labels
    var data = dt.map(item => item.cant); // Extrae los datos
    var myChart2 = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: labels,
                data: data,
                backgroundColor:  [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)'
                  ], // Color de fondo de las barras
                borderColor: 'rgba(54, 162, 235, 1)', // Color del borde de las barras
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true // Empieza el eje y desde cero
                }
            }
        },
        plugins: [ChartDataLabels]
    });
})

$.get('/renunciasxGenero',function(dt){
    const labelGenero = dt.map(dt=>dt.genero);
    const qtyGen=dt.map(dt=>dt.poblacionActiva);
    //var renunciasPorEdad = [10, 20, 15, 8, 5];
    const canvas3  = document.getElementById('renunciaxrangoedad');
    const ctx3=canvas3.getContext("2d");

    var myChart3 = new Chart(ctx3, {
        type: 'pie',
        data: {
            labels: labelGenero,
            datasets: [{
                label: 'Renuncia por Edades',
                data: qtyGen,
                backgroundColor:  [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)'
                ], // Color de fondo de las barras
                borderColor: 'rgba(54, 162, 235, 1)', // Color del borde de las barras
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true // Empieza el eje y desde cero
                }
            }
        },
        plugins: [ChartDataLabels]
    });
});


$.get('/edadesxestado',function(dt){
    const estados = ['activo', 'inactivo'];
    const rangosEdad = ['18-25', '26-35', '36-45', '46-55', '55+', 'Unknown'];
    const generos = ['Hombre', 'Mujer'];
    const colors = {
        'activo': {
            'Hombre': {
                '18-25': 'rgba(54, 162, 235, 0.6)',
                '26-35': 'rgba(54, 162, 235, 0.4)',
                '36-45': 'rgba(54, 162, 235, 0.2)',
                '46-55': 'rgba(54, 162, 235, 0.1)',
                '55+': 'rgba(54, 162, 235, 0.05)',
                'Unknown': 'rgba(54, 162, 235, 0.8)'
            },
            'Mujer': {
                '18-25': 'rgba(255, 99, 132, 0.6)',
                '26-35': 'rgba(255, 99, 132, 0.4)',
                '36-45': 'rgba(255, 99, 132, 0.2)',
                '46-55': 'rgba(255, 99, 132, 0.1)',
                '55+': 'rgba(255, 99, 132, 0.05)',
                'Unknown': 'rgba(255, 99, 132, 0.8)'
            }
        },
        'inactivo': {
            'Hombre': {
                '18-25': 'rgba(75, 192, 192, 0.6)',
                '26-35': 'rgba(75, 192, 192, 0.4)',
                '36-45': 'rgba(75, 192, 192, 0.2)',
                '46-55': 'rgba(75, 192, 192, 0.1)',
                '55+': 'rgba(75, 192, 192, 0.05)',
                'Unknown': 'rgba(75, 192, 192, 0.8)'
            },
            'Mujer': {
                '18-25': 'rgba(153, 102, 255, 0.6)',
                '26-35': 'rgba(153, 102, 255, 0.4)',
                '36-45': 'rgba(153, 102, 255, 0.2)',
                '46-55': 'rgba(153, 102, 255, 0.1)',
                '55+': 'rgba(153, 102, 255, 0.05)',
                'Unknown': 'rgba(153, 102, 255, 0.8)'
            }
        }
    }
    const datasets = [];
    generos.forEach(genero => {
        rangosEdad.forEach(rango_edad => {
            estados.forEach(estado => {
                const entry = dt.find(d => d.estado === estado && d.genero === genero && d.rango_edad === rango_edad);
                if (entry) {
                    const label = `${genero} ${rango_edad} ${estado}`;
                    datasets.push({
                        label: label,
                        data: estados.map(e => e === estado ? entry.poblacionActiva : 0),
                        backgroundColor: colors[estado][genero][rango_edad],
                        stack: `${genero} ${rango_edad}`
                    });
                }
            });
        });
    });

    const ctx4 = document.getElementById('estadopoblacion').getContext('2d');
 
    const config = {
        type: 'bar',
        data: {
            labels: estados,
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true
                }
            }
        },
        plugins: [ChartDataLabels]
    };

    const myChart = new Chart(ctx4, config);
    console.log(dt)
})

let myChart6;
// Inicializar el date picker
function initializeChart() {
    const ctx6 = document.getElementById('grafica6').getContext('2d');
    myChart6 = new Chart(ctx6, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Ingresos',
                data: [],
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    formatter: (value, context) => value,
                    color: 'black'
                }
            },
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true
                }
            }
        },
        plugins: [ChartDataLabels]
    });
}

function destroyChart() {
    if (myChart6) {
        myChart6.destroy();
    }
}
initializeChart();



// Otherwise, selectors are also supported
// Inicializar el date picker
// Inicializar el date picker
flatpickr("#datePicker", {
    mode: "range",
    dateFormat: "Y-m-d",
    onChange: function(selectedDates, dateStr, instance) {
        if (selectedDates.length === 2) {
            const startDate = selectedDates[0];
            const endDate = selectedDates[1];
            
            updateChartData(startDate, endDate, document.getElementById('periodSelector').value);
        }
    }
});

 
// Agregar evento al selector de períodos
document.getElementById('periodSelector').addEventListener('change', function() {
    const dateRange = document.getElementById('datePicker')._flatpickr.selectedDates;
    if (dateRange.length === 2) {
        updateChartData(dateRange[0], dateRange[1], this.value);
    }
});
// Función para actualizar los datos de la gráfica

async function updateChartData(startDate, endDate, period) {
    destroyChart();
    // Datos obtenidos de la peticion con egresos
 
    var response= await $.get('/egresosxingresos')

    // Filtrar los datos según el rango de fechas seleccionado
    const filteredData = response.filter(d => {
        const date = new Date(d.fecha);
        return date >= startDate && date <= endDate;
    });

    // Procesar los datos según el período seleccionado
    const groupedData = groupDataByPeriod(filteredData, period);

    // Extraer las etiquetas (fechas), ingresos y egresos
    const labels = groupedData.map(d => d.label);
    const ingresos = groupedData.map(d => d.ingresos);
    const egresos = groupedData.map(d => d.egresos);

    // Crear un nuevo chart con los datos actualizados
    const ctx7 = document.getElementById('grafica6').getContext('2d');
    myChart6 = new Chart(ctx7, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Ingresos',
                    data: ingresos,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Egresos',
                    data: egresos,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            plugins: {
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    formatter: (value, context) => value,
                    color: 'black'
                }
            },
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true
                }
            }
        },
        plugins: [ChartDataLabels]
    });
}

// Función para agrupar datos según el período seleccionado
function groupDataByPeriod(data, period) {
    const grouped = {};

    data.forEach(d => {
        const date = new Date(d.fecha);
        let label;
        switch (period) {
            case 'day':
                label = date.toISOString().split('T')[0];
                break;
            case 'month':
                label = `${date.getFullYear()}-${('0' + (date.getMonth() + 1)).slice(-2)}`;
                break;
            case 'bimester':
                const bimestre = Math.floor(date.getMonth() / 2) + 1;
                label = `${date.getFullYear()}-B${bimestre}`;
                break;
            case 'trimester':
                const trimester = Math.floor(date.getMonth() / 3) + 1;
                label = `${date.getFullYear()}-T${trimester}`;
                break;
            case 'year':
                label = date.getFullYear().toString();
                break;
            default:
                label = date.toISOString().split('T')[0];
        }

        if (!grouped[label]) {
            grouped[label] = { ingresos: 0, egresos: 0 };
        }
        grouped[label].ingresos += d.ingresos;
        grouped[label].egresos += d.egresos;
    });

    return Object.keys(grouped).map(label => ({
        label: label,
        ingresos: grouped[label].ingresos,
        egresos: grouped[label].egresos
    }));
}

$(document).on('click','#btnContrast',function(e){
    console.log()
    var iconContrast= $(this).html()
    const bgGradient=$('.bg-gradient-dark')
    const bgGradient2=$('.bg-gradient-light')
    if(iconContrast==='<i class="ri-contrast-2-fill"></i>')
    {
        $(this).html('<i class="ri-sun-line"></i>')
        bgGradient.removeClass('bg-gradient-dark').addClass('bg-gradient-light')
        localStorage.setItem('theme','light')
    }else{
        $(this).html('<i class="ri-contrast-2-fill"></i>')
        
        bgGradient2.removeClass('bg-gradient-light').addClass('bg-gradient-dark')
        localStorage.setItem('theme','dark')
    }
})


   const monitorSesiones=()=>
   {
       /* const response =await axios.get('/monitor-sesion');

        const data= response.data

        console.log(data)*/

        $('#usersTable').DataTable({

            ajax:{
                url:'/monitor-sesion',
                dataSrc:'',
            },
            initComplete:function(){
                var searchInput = $('#dt-search-0');
                    searchInput.addClass('border');
            },
            columns:[
                    { data: 'id' },
                    { data: 'name' },
                    { data: 'nombre' },
                    { data: 'perfilesdescrip' },
                    { 
                        data: 'last_session', 
                        render: function(data) {
                            return data ? new Date(data).toLocaleDateString('es-HN', {
                                day: 'numeric',
                                month: 'short',
                                year: 'numeric'
                              }) : 'Sin sesión';
                        }
                    },
                    { 
                        data: 'status',
                        render: function(data) {
                            return data === 1 ? 'Activo' : 'Inactivo';
                        }
                    }
            ]
        })
   }

    monitorSesiones()

}


