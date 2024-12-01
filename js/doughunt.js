const ctx = document.getElementById("doughnut").getContext("2d");
const myChart = new Chart(ctx, {
  type: "doughnut",
  data: {
    labels: ["Usulan", "Disposisi", "Verifikasi", "Pengesahan"],
    datasets: [
      {
        label: "Jumlah Data",
        data: [3, 4, 6, 4], // Sesuaikan data ini sesuai kebutuhan
        backgroundColor: ["#6a0dad", "#1e90ff", "#ffa500", "#20b2aa"],
        borderWidth: 1,
      },
    ],
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: "top",
      },
      tooltip: {
        enabled: true,
      },
    },
  },
});
