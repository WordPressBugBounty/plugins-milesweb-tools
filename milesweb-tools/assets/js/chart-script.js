// assets/js/chart-script.js
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('myChart');

    if (ctx && storageData.data) {
        const storageInfo = storageData.data;

        // Prepare labels and data
        const labels = Object.keys(storageInfo).filter(key => key !== 'Total'); // Exclude "Total"
        const data = labels.map(label => storageInfo[label]); // Values for each category

        // Create pie chart
        const myChart = new Chart(ctx.getContext('2d'), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Storage Usage (MB)',
                    data: data,
                    backgroundColor: [
                        '#9bc4ff',  
                        '#55d490',  
                        '#13b2da',  
                        '#256df0',  
                    ],
                    borderColor: [
                        'rgba(173, 216, 230, 1)',
                        'rgba(100, 149, 237, 1)',
                        'rgba(0, 123, 255, 1)',  
                        'rgba(70, 130, 180, 1)', 
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                },
            },
        });
    }
});
