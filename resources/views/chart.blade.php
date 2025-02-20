<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Sensor</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SheetJS for Excel Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }

        .chart-container {
            position: relative;
            height: 400px;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Grafik Monitoring Sensor</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Filter Data</label>
                                <select id="filter" class="form-select">
                                    <option value="6h">6 Jam Terakhir</option>
                                    <option value="12h" selected>12 Jam Terakhir</option>
                                    <option value="1month">1 Bulan Terakhir</option>
                                    <option value="date">Per Tanggal</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pilih Tanggal</label>
                                <input type="date" id="datePicker" class="form-control" style="display: none;">
                            </div>
                        </div>

                        <div class="chart-container">
                            <canvas id="sensorChart"></canvas>
                        </div>
                    </div>
                    <div class="card-footer text-muted text-center">
                        <button id="exportChart" class="btn btn-success btn-sm">Export Chart (PNG)</button>
                        <button id="exportExcel" class="btn btn-success btn-sm">Export to Excel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>


    <script>
        let sensorChart;

        function fetchData(filter, date = null) {
            $.get("{{ route('chart.data') }}", {
                filter,
                date
            }, function(response) {
                updateChart(response);
            });
        }

        function updateChart(data) {
            if (sensorChart && typeof sensorChart.destroy === "function") {
                sensorChart.destroy();
            }

            let ctx = document.getElementById('sensorChart').getContext('2d');
            sensorChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                            label: "Suhu (°C)",
                            data: data.suhu,
                            borderColor: "red",
                            backgroundColor: "rgba(255,0,0,0.2)",
                            fill: true
                        },
                        {
                            label: "Kelembapan Udara (%)",
                            data: data.kelembapan,
                            borderColor: "blue",
                            backgroundColor: "rgba(0,0,255,0.2)",
                            fill: true
                        },
                        {
                            label: "Kelembapan Tanah (%)",
                            data: data.kelembapanTanah,
                            borderColor: "green",
                            backgroundColor: "rgba(0,255,0,0.2)",
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: "top"
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `${tooltipItem.dataset.label}: ${tooltipItem.raw} %`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            window.sensorChart = sensorChart;
        }

        $(document).ready(function() {
            fetchData("12h");

            $("#filter").change(function() {
                let filter = $(this).val();
                if (filter === "date") {
                    $("#datePicker").show();
                } else {
                    $("#datePicker").hide();
                    fetchData(filter);
                }
            });

            $("#datePicker").change(function() {
                fetchData("date", $(this).val());
            });
        });

        // EXPORT TO EXCEL
        document.getElementById("exportExcel").addEventListener("click", async function() {
            if (!window.sensorChart) {
                alert("Grafik belum tersedia! Pastikan data sudah dimuat.");
                return;
            }

            let labels = window.sensorChart.data.labels;
            let datasets = window.sensorChart.data.datasets;

            let selectedDate = document.getElementById("datePicker").value;
            let exportDate = selectedDate || new Date().toISOString().split('T')[0];

            let workbook = new ExcelJS.Workbook();
            let worksheet = workbook.addWorksheet("Sensor Data");

            // ✅ Tambahkan Judul di Header
            worksheet.mergeCells("A1:F1");
            let titleRow = worksheet.getCell("A1");
            titleRow.value = `Export Data Tanggal ${exportDate} - PT ABCDEX`;
            titleRow.font = {
                bold: true,
                size: 14,
                color: {
                    argb: "FFFFFF"
                }
            };
            titleRow.alignment = {
                horizontal: "center"
            };
            titleRow.fill = {
                type: "pattern",
                pattern: "solid",
                fgColor: {
                    argb: "007BFF"
                }
            };

            // ✅ Buat Data untuk Tabel
            let tableData = [
                ["Tanggal", "Waktu", "Suhu (°C)", "Kelembapan Udara (%)", "Kelembapan Tanah (%)", "Kondisi"]
            ];

            labels.forEach((label, index) => {
                let suhu = datasets[0].data[index];
                let kelembapanUdara = datasets[1].data[index];
                let kelembapanTanah = datasets[2].data[index];

                let kondisi = suhu < 20 ? "Dingin" : suhu <= 30 ? "Sedang" : "Panas";
                tableData.push([exportDate, label, `${suhu} °C`, `${kelembapanUdara} %`,
                    `${kelembapanTanah} %`, kondisi
                ]);
            });

            // ✅ Tambahkan Tabel di Excel
            worksheet.addTable({
                name: "SensorTable",
                ref: "A3", // Mulai dari sel A3
                headerRow: true,
                totalsRow: false,
                style: {
                    theme: "TableStyleMedium9", // Tema default Excel
                    showRowStripes: true,
                },
                columns: [{
                        name: "Tanggal"
                    },
                    {
                        name: "Waktu"
                    },
                    {
                        name: "Suhu (°C)"
                    },
                    {
                        name: "Kelembapan Udara (%)"
                    },
                    {
                        name: "Kelembapan Tanah (%)"
                    },
                    {
                        name: "Kondisi"
                    },
                ],
                rows: tableData.slice(1), // Data tanpa header
            });

            // ✅ Atur Warna di Kolom "Kondisi"
            let kondisiColumnIndex = 6; // Kolom "Kondisi" ada di kolom ke-6 (F)
            worksheet.eachRow((row, rowNumber) => {
                if (rowNumber > 3) { // Lewati header & judul
                    let kondisiCell = row.getCell(kondisiColumnIndex);
                    if (kondisiCell.value === "Dingin") {
                        kondisiCell.fill = {
                            type: "pattern",
                            pattern: "solid",
                            fgColor: {
                                argb: "ADD8E6"
                            }
                        }; // Biru
                    } else if (kondisiCell.value === "Sedang") {
                        kondisiCell.fill = {
                            type: "pattern",
                            pattern: "solid",
                            fgColor: {
                                argb: "90EE90"
                            }
                        }; // Hijau
                    } else {
                        kondisiCell.fill = {
                            type: "pattern",
                            pattern: "solid",
                            fgColor: {
                                argb: "FF6347"
                            }
                        }; // Merah
                    }
                }
            });

            // ✅ Simpan File Excel
            let buffer = await workbook.xlsx.writeBuffer();
            let filename = `Export_Data_${exportDate}.xlsx`;
            saveAs(new Blob([buffer], {
                type: "application/octet-stream"
            }), filename);
        });







        // EXPORT CHART TO IMAGE
        document.getElementById("exportChart").addEventListener("click", function() {
            let canvas = document.getElementById("sensorChart");
            let image = canvas.toDataURL("image/png");

            let link = document.createElement("a");
            link.href = image;
            link.download = "SensorChart.png";
            link.click();
        });
    </script>

</body>

</html>
