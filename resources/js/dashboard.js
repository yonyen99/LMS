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
    const departmentChart = new Chart(departmentCtx, {
        type: "pie",
        data: {
            labels: departmentData.map(item => item.name),
            datasets: [
                {
                    data: departmentData.map(item => item.user_count),
                    backgroundColor: [
                        "rgba(66, 133, 244, 0.6)",  // Blue
                        "rgba(234, 67, 53, 0.6)",   // Red
                        "rgba(52, 168, 83, 0.6)",   // Green
                        "rgba(251, 188, 5, 0.6)",   // Yellow
                        "rgba(156, 39, 176, 0.6)",  // Purple
                        "rgba(244, 81, 30, 0.6)",   // Orange
                        "rgba(117, 117, 117, 0.6)", // Grey
                        "rgba(0, 172, 193, 0.6)",   // Cyan
                        "rgba(139, 195, 74, 0.6)",  // Light Green
                        "rgba(233, 30, 99, 0.6)",   // Pink
                        "rgba(103, 58, 183, 0.6)",  // Deep Purple
                        "rgba(255, 112, 67, 0.6)",  // Deep Orange
                        "rgba(3, 169, 244, 0.6)",   // Light Blue
                        "rgba(121, 85, 72, 0.6)",   // Brown
                        "rgba(205, 220, 57, 0.6)",  // Lime
                        "rgba(96, 125, 139, 0.6)"   // Blue Grey
                    ],
                    hoverBackgroundColor: [
                        "rgba(66, 133, 244, 0.8)",
                        "rgba(234, 67, 53, 0.8)",
                        "rgba(52, 168, 83, 0.8)",
                        "rgba(251, 188, 5, 0.8)",
                        "rgba(156, 39, 176, 0.8)",
                        "rgba(244, 81, 30, 0.8)",
                        "rgba(117, 117, 117, 0.8)",
                        "rgba(0, 172, 193, 0.8)",
                        "rgba(139, 195, 74, 0.8)",
                        "rgba(233, 30, 99, 0.8)",
                        "rgba(103, 58, 183, 0.8)",
                        "rgba(255, 112, 67, 0.8)",
                        "rgba(3, 169, 244, 0.8)",
                        "rgba(121, 85, 72, 0.8)",
                        "rgba(205, 220, 57, 0.8)",
                        "rgba(96, 125, 139, 0.8)"
                    ],
                    borderWidth: 2,
                    hoverBorderWidth: 3,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: 30
            },
            plugins: {
                title: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            const index = context.dataIndex;
                            const department = departmentData[index] || {};
                            const label = department.name || 'Unknown Department';
                            const total = department.user_count || 0;
                            const managers = department.manager_names || [];
                            const employees = department.employee_names || [];
                            const managerText = managers.length > 0 ? `Manager${managers.length > 1 ? 's' : ''}: ${managers.join(', ')}` : 'No Managers';
                            const employeeText = employees.length > 0 ? `Employee${employees.length > 1 ? 's' : ''}: ${employees.join(', ')}` : 'No Employees';
                            return [
                                `${label}: ${total} member${total !== 1 ? 's' : ''}`,
                                managerText,
                                employeeText
                            ];
                        }
                    },
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12
                    },
                    padding: 10,
                    cornerRadius: 4
                },
                legend: {
                    position: 'right',
                    maxWidth: 300,
                    labels: {
                        boxWidth: 20,
                        padding: 25,
                        font: {
                            size: 11
                        },
                        usePointStyle: true,
                        generateLabels: function(chart) {
                            const data = chart.data;
                            return data.labels.map((label, index) => ({
                                text: label,
                                fillStyle: data.datasets[0].backgroundColor[index],
                                strokeStyle: data.datasets[0].borderColor ? data.datasets[0].borderColor[index] : data.datasets[0].backgroundColor[index],
                                lineWidth: data.datasets[0].borderWidth,
                                hidden: chart.getDatasetMeta(0).data[index].hidden,
                                index: index,
                                pointStyle: 'circle'
                            }));
                        }
                    }
                }
            },
            hover: {
                mode: 'nearest',
                intersect: true
            }
        },
    });
} else {
    console.warn('No department data available for the chart.');
}