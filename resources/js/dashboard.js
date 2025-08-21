// Log to verify monthlyRequestData
console.log('Received monthlyRequestData:', window.monthlyRequestData);

// Ensure monthlyRequestData is defined and valid
const monthlyRequestDataSafe = typeof window.monthlyRequestData !== 'undefined' && Array.isArray(window.monthlyRequestData) 
    ? window.monthlyRequestData 
    : Array(12).fill(0);

// Log the safe data for debugging
console.log('monthlyRequestDataSafe:', monthlyRequestDataSafe);

// Get the current month (0-based index, e.g., August = 7)
const currentMonthIndex = new Date().getMonth(); // 0 for Jan, 7 for Aug, etc.

const employeeCtx = document.getElementById("employeeChart");
if (!employeeCtx) {
    console.error('Canvas element with id "employeeChart" not found.');
} else {
    const employeeChart = new Chart(employeeCtx.getContext("2d"), {
        type: "bar",
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [
                {
                    label: "Approved Leave Requests",
                    data: monthlyRequestDataSafe,
                    backgroundColor: "rgba(54, 162, 235, 0.6)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Approved Requests'
                    },
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    },
                    ticks: {
                        color: function(context) {
                            // Set blue color for the current month, black for others
                            return context.index === currentMonthIndex ? '#0000FF' : '#000000';
                        },
                        font: function(context) {
                            // Optional: Make the current month bold for extra emphasis
                            return context.index === currentMonthIndex 
                                ? { weight: 'bold' } 
                                : { weight: 'normal' };
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Approved Requests: ${context.raw}`;
                        }
                    }
                }
            }
        }
    });
}

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