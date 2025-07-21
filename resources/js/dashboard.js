
const employeeCtx = document.getElementById("employeeChart").getContext("2d");
const employeeChart = new Chart(employeeCtx, {
    type: "bar",
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "Jun", "Jul", "Aug", "Sep"],
        datasets: [
            {
                label: "Requests",
                data: [5, 10, 15, 20, 25, 30, 35, 40],
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

const departmentCtx = document
    .getElementById("departmentChart")
    .getContext("2d");
const departmentChart = new Chart(departmentCtx, {
    type: "pie",
    data: {
        labels: ["HR", "Developer", "Security", "IT Support"],
        datasets: [
            {
                data: [20, 30, 30, 20],
                backgroundColor: [
                    "rgba(54, 162, 235, 0.6)",
                    "rgba(255, 99, 132, 0.6)",
                    "rgba(75, 192, 192, 0.6)",
                ],
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
    },
});
