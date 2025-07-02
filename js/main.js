// Configuração dos cards
const cards = [
  {
    id: "temperatura",
    title: "Temperatura",
    icon: "fa-temperature-high",
    unit: "°C",
    color: "danger",
  },
  {
    id: "umidade",
    title: "Umidade",
    icon: "fa-tint",
    unit: "%",
    color: "info",
  },
  {
    id: "uv",
    title: "Índice UV",
    icon: "fa-sun",
    unit: "mW/cm²",
    color: "warning",
  },
  {
    id: "luminosidade",
    title: "Luminosidade",
    icon: "fa-lightbulb",
    unit: "lux",
    color: "warning",
  },
  {
    id: "vento",
    title: "Velocidade do Vento",
    icon: "fa-wind",
    unit: "km/h",
    color: "primary",
  },
  {
    id: "direcao",
    title: "Direção do Vento",
    icon: "fa-compass",
    unit: "",
    color: "success",
  },
  {
    id: "chuva",
    title: "Precipitação",
    icon: "fa-cloud-rain",
    unit: "mm",
    color: "info",
  },
];

// Inicialização
document.addEventListener("DOMContentLoaded", function () {
  initializeThemeToggle();
  initializeSortable();
  updateData();
  initializeMainChart();
  setInterval(updateData, 60000); // Atualiza a cada minuto
});

// Atualização dos Dados
function updateData() {
  fetch("api/get_data.php?latest=true")
    .then((response) => response.json())
    .then((data) => {
      updateCardValue("temperatura", data.temperatura);
      updateCardValue("umidade", data.umidade);
      updateCardValue("uv", data.uv);
      updateCardValue("luminosidade", data.luminosidade);
      updateCardValue("vento", data.velocidade_vento);
      updateWindDirection(data.direcao_vento);
      updateCardValue("chuva", data.precipitacao);
      updateLastUpdate(data.data_hora);
      updateStatus(data);
    })
    .catch((error) => console.error("Erro ao atualizar dados:", error));
}

// Atualização dos Valores dos Cards
function updateCardValue(cardId, value) {
  const element = document.getElementById(`value-${cardId}`);
  if (element) {
    element.textContent = value || "--";
    element.classList.add("pulse");
    setTimeout(() => element.classList.remove("pulse"), 1000);
  }
}

// Atualização da Direção do Vento
function updateWindDirection(direction) {
  const arrow = document.getElementById("windArrow");
  const directionText = document.getElementById("windDirection");

  if (arrow && directionText) {
    const degrees = getWindDirectionDegrees(direction);
    arrow.style.transform = `rotate(${degrees}deg)`;
    directionText.textContent = direction || "--";
  }
}

// Conversão da Direção do Vento para Graus
function getWindDirectionDegrees(direction) {
  const directions = {
    N: 0,
    NNE: 22.5,
    NE: 45,
    ENE: 67.5,
    E: 90,
    ESE: 112.5,
    SE: 135,
    SSE: 157.5,
    S: 180,
    SSW: 202.5,
    SW: 225,
    WSW: 247.5,
    W: 270,
    WNW: 292.5,
    NW: 315,
    NNW: 337.5,
  };
  return directions[direction] || 0;
}

// Atualização da Última Atualização
function updateLastUpdate(timestamp) {
  const date = new Date(timestamp);
  const formattedDate = date.toLocaleString("pt-BR");
  document.getElementById("lastUpdate").textContent = formattedDate;
}

// Atualização do Status
function updateStatus(data) {
  const status = document.getElementById("currentStatus");
  const statusIcon = document.querySelector(".status-icon i");

  let weatherStatus = "Ensolarado";
  let icon = "fa-cloud-sun";

  if (data.umidade > 80) {
    weatherStatus = "Chuvoso";
    icon = "fa-cloud-rain";
  } else if (data.umidade > 60) {
    weatherStatus = "Nublado";
    icon = "fa-cloud";
  }

  status.textContent = weatherStatus;
  statusIcon.className = `fas ${icon}`;
}

// Alternância do Tema
function initializeThemeToggle() {
  const themeToggle = document.getElementById("themeToggle");
  themeToggle.addEventListener("click", () => {
    document.body.classList.toggle("dark-theme");
    document.body.classList.toggle("light-theme");
    themeToggle.querySelector("i").classList.toggle("fa-moon");
    themeToggle.querySelector("i").classList.toggle("fa-sun");
    updateChartTheme();
  });
}
function initializeSortable() {
  const dashboard = document.getElementById("dashboard");
  let originalOrder = [];

  new Sortable(dashboard, {
    animation: 150,
    handle: ".move-card",
    ghostClass: "card-ghost",
    filter: ".locked",

    onStart: function () {
      originalOrder = Array.from(dashboard.children).map((el) => el.id);
    },

    onMove: function (evt) {
      const dragged = evt.dragged;
      const target = evt.related;

      // Verifica se o target (posição de destino) é um card locked
      if (target.classList.contains("locked")) {
        return false;
      }

      // Verifica se o card sendo arrastado é locked (redundância segura)
      if (dragged.classList.contains("locked")) {
        return false;
      }

      return true;
    },

    onEnd: function () {
      const currentOrder = Array.from(dashboard.children).map((el) => el.id);

      // Verifica se algum card locked mudou de posição
      for (let i = 0; i < originalOrder.length; i++) {
        const originalId = originalOrder[i];
        const originalEl = document.getElementById(originalId);

        if (
          originalEl.classList.contains("locked") &&
          currentOrder[i] !== originalId
        ) {
          restoreOrder(originalOrder);
          return;
        }
      }
    },
  });

  function restoreOrder(order) {
    const fragment = document.createDocumentFragment();
    order.forEach((id) => {
      const el = document.getElementById(id);
      if (el) {
        fragment.appendChild(el);
      }
    });
    dashboard.appendChild(fragment);
  }
}

// Bloqueio/Desbloqueio de Cards
document.addEventListener("click", function (e) {
  if (e.target.closest(".lock-card")) {
    const button = e.target.closest(".lock-card");
    const card = button.closest(".card");
    const column = card.closest(".col-md-4"); // ou .col-lg-3
    const icon = button.querySelector("i");

    if (icon.classList.contains("fa-lock")) {
      icon.classList.replace("fa-lock", "fa-lock-open");
      column.classList.remove("locked");
    } else {
      icon.classList.replace("fa-lock-open", "fa-lock");
      column.classList.add("locked");
    }
  }
});

// Inicialização do Gráfico Principal
function initializeMainChart() {
  Highcharts.chart("mainChart", {
    chart: {
      type: "line",
      style: {
        fontFamily: "Segoe UI, Tahoma, Geneva, Verdana, sans-serif",
      },
    },
    title: {
      text: null,
    },
    xAxis: {
      type: "datetime",
      labels: {
        format: "{value:%d/%m %H:%M}",
      },
    },
    yAxis: {
      title: {
        text: "Valores",
      },
    },
    tooltip: {
      shared: true,
      crosshairs: true,
      xDateFormat: "%d/%m/%Y %H:%M",
    },
    plotOptions: {
      series: {
        marker: {
          enabled: false,
        },
      },
    },
    series: cards.map((card) => ({
      name: card.title,
      data: [],
    })),
  });

  updateChartData();
}

// Atualização dos Dados do Gráfico
function updateChartData() {
  const period = document.querySelector(".btn-group .btn.active").dataset
    .period;

  fetch(`api/get_data.php?period=${period}`)
    .then((response) => response.json())
    .then((data) => {
      const chart = Highcharts.charts[0];
      const series = cards.map((card) => ({
        name: card.title,
        data: data.map((item) => [
          new Date(item.data_hora).getTime(),
          parseFloat(item[card.id]) || null,
        ]),
      }));

      chart.update({ series });
    })
    .catch((error) =>
      console.error("Erro ao atualizar dados do gráfico:", error)
    );
}

// Atualização do Tema do Gráfico
function updateChartTheme() {
  const chart = Highcharts.charts[0];
  if (chart) {
    const isDark = document.body.classList.contains("dark-theme");
    chart.update({
      chart: {
        backgroundColor: isDark ? "#1e2124" : "#ffffff",
      },
      xAxis: {
        labels: {
          style: {
            color: isDark ? "#ffffff" : "#333333",
          },
        },
      },
      yAxis: {
        labels: {
          style: {
            color: isDark ? "#ffffff" : "#333333",
          },
        },
      },
    });
  }
}

// Exportação de Dados
function exportData() {
  fetch("api/get_data.php?period=month")
    .then((response) => response.json())
    .then((data) => {
      const table = document.createElement("table");
      table.id = "exportTable";

      // Cabeçalho
      const thead = document.createElement("thead");
      thead.innerHTML = `
                <tr>
                    <th>Data/Hora</th>
                    <th>Temperatura (°C)</th>
                    <th>Umidade (%)</th>
                    <th>UV (mW/cm²)</th>
                    <th>Luminosidade (lux)</th>
                    <th>Vento (km/h)</th>
                    <th>Direção</th>
                    <th>Chuva (mm)</th>
                </tr>
            `;
      table.appendChild(thead);

      // Dados
      const tbody = document.createElement("tbody");
      data.forEach((row) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
                    <td>${new Date(row.data_hora).toLocaleString("pt-BR")}</td>
                    <td>${row.temperatura || "--"}</td>
                    <td>${row.umidade || "--"}</td>
                    <td>${row.uv || "--"}</td>
                    <td>${row.luminosidade || "--"}</td>
                    <td>${row.velocidade_vento || "--"}</td>
                    <td>${row.direcao_vento || "--"}</td>
                    <td>${row.precipitacao || "--"}</td>
                `;
        tbody.appendChild(tr);
      });
      table.appendChild(tbody);

      // Exportação
      const exportTable = new TableExport(table, {
        filename: "dados_estacao_meteorologica",
        sheetname: "Dados",
        type: "xlsx",
      });

      exportTable.export();
    })
    .catch((error) => console.error("Erro ao exportar dados:", error));
}

// Event Listeners para os botões de período
document.querySelectorAll(".btn-group .btn").forEach((button) => {
  button.addEventListener("click", function () {
    document
      .querySelectorAll(".btn-group .btn")
      .forEach((btn) => btn.classList.remove("active"));
    this.classList.add("active");
    updateChartData();
  });
});
