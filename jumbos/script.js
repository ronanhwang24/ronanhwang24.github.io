function getGreeting() {
    const hour = new Date().getHours();
    if (hour < 12) return "Good morning!";
    if (hour < 18) return "Good afternoon!";
    return "Good evening!";
}

document.addEventListener("DOMContentLoaded", () => {
    const greetingEl = document.getElementById("greeting");
    if (greetingEl) {
        greetingEl.textContent = getGreeting();
    }
});

const fileInput = document.getElementById('csvFile');
const uploadStatus = document.getElementById('uploadStatus');
const pitcherSelect = document.getElementById('pitcherSelect');
const customPitcher = document.getElementById('customPitcher');
const timeframeSelect = document.getElementById('timeframeSelect');
const pitchBreakdown = document.getElementById('pitchBreakdown');
const strikeZoneChart = document.getElementById('strikeZoneChart');
const movementChart = document.getElementById('movementChart');

let dataset = [];

const pitchColors = {
    Fastball: '#bf0000',      
    Slider: '#ffcc00',       
    Curveball: '#00a8a8',    
    ChangeUp: '#2ca02c',     
    Sinker: '#e39300',       
    Cutter: '#8b4513',       
    Splitter: '#008080',     
    Knuckleball: '#000000',  
    Other: '#808080'
};



fileInput.addEventListener('change', (event) => {
  const file = event.target.files[0];
  if (file) {
    Papa.parse(file, {
      header: true,
      skipEmptyLines: true,
      complete: function(results) {
        uploadStatus.textContent = "File uploaded successfully!";
        dataset = results.data;
        clearDisplay();
      }
    });
  }
});

function populateTimeframes(pitcherName) {
  const pitcherGames = dataset.filter(row => row.pitcher_name === pitcherName);
  const uniqueGames = [...new Map(pitcherGames.map(row => [row.game_pk, row])).values()];

  timeframeSelect.innerHTML = '<option value="all">Select All</option>';
  uniqueGames.forEach(row => {
    const label = `${row.game_date}, ${row.home_team_abbrev} @ ${row.away_team_abbrev}`;
    const option = document.createElement('option');
    option.value = row.game_pk;
    option.textContent = label;
    timeframeSelect.appendChild(option);
  });
}


function filterAndDisplay(pitcherName) {
  if (!pitcherName) {
    clearDisplay();
    return;
  }

  let filtered = dataset.filter(row => row.pitcher_name === pitcherName);
  const selectedGames = Array.from(timeframeSelect.selectedOptions).map(opt => opt.value);
  if (!selectedGames.includes("all")) {
    filtered = filtered.filter(row => selectedGames.includes(row.game_pk));
  }

  // Table preview logic (hidden from UI)
  if (filtered.length > 0) {
    const headers = Object.keys(filtered[0]);
    let html = "<table><thead><tr>";
    headers.forEach(header => {
      html += `<th>${header}</th>`;
    });
    html += "</tr></thead><tbody>";
    filtered.forEach(row => {
      html += "<tr>";
      headers.forEach(header => {
        html += `<td>${row[header]}</td>`;
      });
      html += "</tr>";
    });
    html += "</tbody></table>";
  }
  // Batter side filter
const batterSide = document.querySelector('input[name="batterSide"]:checked').value;
if (batterSide === "left") {
  filtered = filtered.filter(row => row.batter_side === "Left");
} else if (batterSide === "right") {
  filtered = filtered.filter(row => row.batter_side === "Right");
}


  // Pitch breakdown
const pitchTypes = [...new Set(filtered.map(row => row.pitch_type).filter(Boolean))];
const pitchStats = [];

pitchTypes.forEach(pitch => {
    const pitches = filtered.filter(row => row.pitch_type === pitch);
    const count = pitches.length;
    const percent = ((count / filtered.length) * 100).toFixed(2);
    const speeds = pitches.map(row => parseFloat(row.release_speed)).filter(v => !isNaN(v));
    const max = Math.max(...speeds).toFixed(1);
    const min = Math.min(...speeds).toFixed(1);
    const avg = (speeds.reduce((a, b) => a + b, 0) / speeds.length).toFixed(1);
  
    // Count breakdowns
    const count_1_1 = pitches.filter(p => p.balls === "1" && p.strikes === "1");
    const count_02_12 = pitches.filter(p =>
      (p.balls === "0" && p.strikes === "2") || (p.balls === "1" && p.strikes === "2")
    );
    const count_22_32 = pitches.filter(p =>
      (p.balls === "2" && p.strikes === "2") || (p.balls === "3" && p.strikes === "2")
    );
  
    const format = (subset) => `${subset.length} (${((subset.length / count) * 100).toFixed(1)}%)`;
  
    pitchStats.push({
      pitch,
      count,
      percent,
      max,
      min,
      avg,
      "1-1": format(count_1_1),
      "0-2/1-2": format(count_02_12),
      "2-2/3-2": format(count_22_32)
    });
  });

  const headers = [
    "Pitch Type", "Count", "%", "Max Velo", "Min Velo", "Avg Velo",
    "1-1", "0-2/1-2", "2-2/3-2"
  ];
  
  let html = "<table><thead><tr>";
  headers.forEach(header => {
    html += `<th>${header}</th>`;
  });
  html += "</tr></thead><tbody>";
  
  pitchStats.forEach(stat => {
    html += "<tr>";
    headers.forEach(header => {
      html += `<td>${stat[header] || ""}</td>`;
    });
    html += "</tr>";
  });
  html += "</tbody></table>";

// 🥧 Pie chart
const pieData = [{
  type: 'pie',
  labels: pitchStats.map(p => p.pitch),
  values: pitchStats.map(p => parseFloat(p.percent)),
  textinfo: 'label+percent',
  marker: {
    colors: pitchStats.map(p => pitchColors[p.pitch] || '#000000')
  },
}];

const pieLayout = {
  title: 'Pitch Usage Breakdown',
  height: 400,
  width: 400
};

Plotly.newPlot('pitchPieChart', pieData, pieLayout);


// Table with count breakdowns
let tableHTML = `<table>
  <thead>
    <tr>
      <th>Pitch Type</th>
      <th>Pitches Thrown</th>
      <th>Max Velo</th>
      <th>Min Velo</th>
      <th>Avg Velo</th>
      <th>1-1</th>
      <th>0-2/1-2</th>
      <th>2-2/3-2</th>
    </tr>
  </thead>
  <tbody>`;

pitchStats.forEach(stat => {
  tableHTML += `<tr>
    <td>${stat.pitch}</td>
    <td>${stat.count} (${stat.percent}%)</td>
    <td>${stat.max} mph</td>
    <td>${stat.min} mph</td>
    <td>${stat.avg} mph</td>
    <td>${stat["1-1"]}</td>
    <td>${stat["0-2/1-2"]}</td>
    <td>${stat["2-2/3-2"]}</td>
  </tr>`;
});

tableHTML += "</tbody></table>";
pitchBreakdown.innerHTML = tableHTML;

  // Strike zone chart
  const pitchSamples = {};
  pitchTypes.forEach(type => {
    pitchSamples[type] = filtered
      .filter(row => row.pitch_type === type)
      .map(row => ({
        x: parseFloat(row.plate_location_side),
        y: parseFloat(row.plate_location_height)
      }))
      .filter(p => !isNaN(p.x) && !isNaN(p.y));
  });

  const traces = Object.entries(pitchSamples).map(([type, points]) => ({
  x: points.map(p => p.x),
  y: points.map(p => p.y),
  mode: 'markers',
  type: 'scatter',
  name: type,
  marker: {
    size: 10,
    color: pitchColors[type] || '#000000'
  }
}));


  const layout = {
    title: 'Strike Zone:',
    xaxis: {
      title: 'Plate Location Side',
      range: [-1.5, 1.5],
      zeroline: false,
      scaleanchor: 'y'
    },
    yaxis: {
      title: 'Plate Location Height',
      range: [1, 4],
      zeroline: false
    },
    shapes: [
      { type: 'rect', x0: -0.83, x1: 0.83, y0: 1.5, y1: 3.5, line: { color: 'black' } },
      { type: 'line', x0: -0.28, x1: -0.28, y0: 1.5, y1: 3.5, line: { dash: 'dot' } },
      { type: 'line', x0: 0.28, x1: 0.28, y0: 1.5, y1: 3.5, line: { dash: 'dot' } },
      { type: 'line', x0: -0.83, x1: 0.83, y0: 2.17, y1: 2.17, line: { dash: 'dot' } },
      { type: 'line', x0: -0.83, x1: 0.83, y0: 2.83, y1: 2.83, line: { dash: 'dot' } }
    ]
  };

  Plotly.newPlot('strikeZoneChart', traces, layout);

  // Pitch movement chart
  const movementSamples = {};
  pitchTypes.forEach(type => {
    movementSamples[type] = filtered
      .filter(row => row.pitch_type === type)
      .slice(0, 30)
      .map(row => ({
        x: parseFloat(row.horizontal_break),
        y: parseFloat(row.induced_vertical_break)
      }))
      .filter(p => !isNaN(p.x) && !isNaN(p.y));
  });

  const movementTraces = Object.entries(movementSamples).map(([type, points]) => ({
  x: points.map(p => p.x),
  y: points.map(p => p.y),
  mode: 'markers',
  type: 'scatter',
  name: type,
  marker: {
    size: 10,
    color: pitchColors[type] || '#000000'
  }
}));


  const movementLayout = {
    title: 'Pitch Movement: Horizontal vs Vertical Break',
    xaxis: {
      title: 'Horizontal Break (inches)',
      zeroline: false
    },
    yaxis: {
      title: 'Induced Vertical Break (inches)',
      zeroline: false
    }
  };

  Plotly.newPlot('movementChart', movementTraces, movementLayout);

  // Exit Velocity Box Plot
const exitData = filtered.filter(row => row.hit_exit_speed && !isNaN(parseFloat(row.hit_exit_speed)));
const exitGroups = {};
exitData.forEach(row => {
  const pitch = row.pitch_type;
  const speed = parseFloat(row.hit_exit_speed);
  if (!exitGroups[pitch]) exitGroups[pitch] = [];
  exitGroups[pitch].push(speed);
});

const exitTraces = Object.entries(exitGroups).map(([pitch, speeds]) => ({
  y: speeds,
  type: 'box',
  name: pitch,
  boxpoints: 'outliers',
  marker: { color: '#AB0003' }
}));

Plotly.newPlot('exitVelocityChart', exitTraces, {
  title: 'Exit Velocity by Pitch Type',
  yaxis: { title: 'Exit Velocity (mph)' },
  margin: { t: 40 }
});

// Launch Angle Box Plot
const launchData = filtered.filter(row => row.hit_launch_angle && !isNaN(parseFloat(row.hit_launch_angle)));
const launchGroups = {};
launchData.forEach(row => {
  const pitch = row.pitch_type;
  const angle = parseFloat(row.hit_launch_angle);
  if (!launchGroups[pitch]) launchGroups[pitch] = [];
  launchGroups[pitch].push(angle);
});

const launchTraces = Object.entries(launchGroups).map(([pitch, angles]) => ({
  y: angles,
  type: 'box',
  name: pitch,
  boxpoints: 'outliers',
  marker: { color: '#14225A' }
}));

Plotly.newPlot('launchAngleChart', launchTraces, {
  title: 'Launch Angle by Pitch Type',
  yaxis: { title: 'Launch Angle (°)' },
  margin: { t: 40 }
});

// Spray Chart
const sprayData = filtered.filter(row =>
  row.hit_launch_direction && row.hit_landing_distance &&
  !isNaN(parseFloat(row.hit_launch_direction)) &&
  !isNaN(parseFloat(row.hit_landing_distance))
);

const sprayPoints = {};
sprayData.forEach(row => {
  const pitch = row.pitch_type;
  const angle = parseFloat(row.hit_launch_direction);
  const distance = parseFloat(row.hit_landing_distance);
  if (!sprayPoints[pitch]) sprayPoints[pitch] = [];
  sprayPoints[pitch].push({
    x: distance * Math.sin(angle * Math.PI / 180),
    y: distance * Math.cos(angle * Math.PI / 180)
  });
});

const sprayTraces = Object.entries(sprayPoints).map(([pitch, points]) => ({
  x: points.map(p => p.x),
  y: points.map(p => p.y),
  mode: 'markers',
  type: 'scatter',
  name: pitch,
  marker: { size: 8 }
}));

const sprayLayout = {
  title: 'Spray Chart by Pitch Type',
  xaxis: {
  title: 'Horizontal Direction (feet)',
  range: [-250, 250],
  zeroline: false,
  scaleanchor: 'y'  // 🔒 locks aspect ratio
}
,
  yaxis: {
    title: 'Distance from Home Plate (feet)',
    range: [0, 450],
    zeroline: false
  },
  shapes: [
    { type: 'circle', xref: 'x', yref: 'y', x0: -250, y0: 0, x1: 250, y1: 500, line: { color: 'lightgray' } },
    { type: 'path', path: 'M 0 0 L -250 450 M 0 0 L 250 450', line: { color: 'gray', dash: 'dot' } }
  ]
};

Plotly.newPlot('sprayChart', sprayTraces, sprayLayout);

}

function clearDisplay() {
  strikeZoneChart.innerHTML = "";
  movementChart.innerHTML = "";
  timeframeSelect.innerHTML = '<option value="all">Select All</option>';
}

pitcherSelect.addEventListener('change', () => {
  customPitcher.value = "";
  const selected = pitcherSelect.value;
  populateTimeframes(selected);
  filterAndDisplay(selected);
});

customPitcher.addEventListener('input', () => {
  pitcherSelect.value = "";
  const typed = customPitcher.value.trim();
  if (typed === "") {
    clearDisplay();
  } else {
    populateTimeframes(typed);
    filterAndDisplay(typed);
  }
});

timeframeSelect.addEventListener('change', () => {
  const pitcherName = pitcherSelect.value || customPitcher.value.trim();
  if (pitcherName) {
    filterAndDisplay(pitcherName);
  }
});


document.querySelectorAll('input[name="batterSide"]').forEach(radio => {
  radio.addEventListener('change', () => {
    const pitcherName = pitcherSelect.value || customPitcher.value.trim();
    if (pitcherName) {
      filterAndDisplay(pitcherName);
    }
  });
});
