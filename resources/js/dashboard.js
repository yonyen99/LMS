const employeeCtx = document.getElementById("employeeChart").getContext("2d");
const employeeChart = new Chart(employeeCtx, {
    type: "bar",
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [
            {
                label: "Requests",
                data: [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60],
                backgroundColor: "rgba(54, 162, 235, 0.6)",
                borderColor: "rgba(54, 162, 235, 1)",
                borderWidth: 1,
            },
        ],
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
            },
        },
    },
});

// Initialize department chart only if departmentData exists and is not empty
if (typeof departmentData !== 'undefined' && departmentData.length > 0) {
    const departmentCtx = document.getElementById("departmentChart").getContext("2d");
    new Chart(departmentCtx, {
        type: "pie",
        data: {
            labels: departmentData.map(item => item.name),
            datasets: [{
                data: departmentData.map(item => item.user_count),
                backgroundColor: [
                    "#4285F4", "#EA4335", "#34A853", "#FBBC05",
                    "#9C27B0", "#F4511E", "#757575", "#00ACC1",
                    "#8BC34A", "#E91E63", "#673AB7", "#FF7043",
                    "#03A9F4", "#795548", "#CDDC39", "#607D8B"
                ],
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: 20
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const i = context.dataIndex;
                            const dept = departmentData[i] || {};
                            const label = dept.name || 'Unknown';
                            const count = dept.user_count || 0;
                            const mgrs = dept.manager_names?.join(', ') || 'No Managers';
                            const emps = dept.employee_names?.join(', ') || 'No Employees';

                            return [
                                `${label}: ${count} member${count !== 1 ? 's' : ''}`,
                                `Managers: ${mgrs}`,
                                `Employees: ${emps}`
                            ];
                        }
                    }
                },
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            }
        }
    });
} else {
    console.warn('No department data for chart.');
}
