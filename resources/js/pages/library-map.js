// Library map page specific JavaScript - extracted from inline scripts

// Global variables
let map;
let userMarker;
let libraryMarkers = [];
let userLocation = null;

// Custom icons
const userIcon = L.icon({
    iconUrl:
        "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png",
    shadowUrl:
        "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
});

const libraryIcon = L.icon({
    iconUrl:
        "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png",
    shadowUrl:
        "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
});

// Coordinates for Brazilian states and cities
const stateCoordinates = {
    AC: [-9.0238, -70.812], // Acre
    AL: [-9.5713, -36.782], // Alagoas
    AP: [1.4144, -51.7865], // Amapá
    AM: [-4.1431, -69.8597], // Amazonas
    BA: [-13.2905, -41.711], // Bahia
    CE: [-5.4984, -39.3206], // Ceará
    DF: [-15.7998, -47.8645], // Distrito Federal
    ES: [-19.1834, -40.3089], // Espírito Santo
    GO: [-15.827, -49.8362], // Goiás
    MA: [-4.9609, -45.2744], // Maranhão
    MT: [-12.6819, -56.9211], // Mato Grosso
    MS: [-20.7722, -54.7852], // Mato Grosso do Sul
    MG: [-18.5122, -44.555], // Minas Gerais
    PA: [-3.9014, -52.4774], // Pará
    PB: [-7.2399, -36.7819], // Paraíba
    PR: [-24.89, -51.55], // Paraná
    PE: [-8.8137, -36.9541], // Pernambuco
    PI: [-8.5569, -42.7401], // Piauí
    RJ: [-22.9099, -43.2095], // Rio de Janeiro
    RN: [-5.4026, -36.9541], // Rio Grande do Norte
    RS: [-30.0346, -51.2177], // Rio Grande do Sul
    RO: [-10.9472, -62.8484], // Rondônia
    RR: [1.99, -61.33], // Roraima
    SC: [-27.2423, -50.2189], // Santa Catarina
    SP: [-23.5505, -46.6333], // São Paulo
    SE: [-10.5741, -37.3857], // Sergipe
    TO: [-10.184, -48.3336], // Tocantins
};

const cityCoordinates = {
    AC: {
        "Rio Branco": [-9.9754, -67.8249],
        "Cruzeiro do Sul": [-7.6278, -72.6761],
    },
    AL: {
        Maceió: [-9.6658, -35.7353],
        Arapiraca: [-9.7515, -36.6608],
    },
    AP: {
        Macapá: [0.0389, -51.0664],
        Santana: [-0.0583, -51.1817],
    },
    AM: {
        Manaus: [-3.119, -60.0217],
        Parintins: [-2.6287, -56.7356],
    },
    BA: {
        Salvador: [-12.9714, -38.5014],
        "Feira de Santana": [-12.2664, -38.9663],
        "Vitória da Conquista": [-14.8619, -40.8444],
    },
    CE: {
        Fortaleza: [-3.7319, -38.5267],
        Sobral: [-3.688, -40.3496],
    },
    DF: {
        Brasília: [-15.7942, -47.8822],
    },
    ES: {
        Vitória: [-20.3155, -40.3128],
        "Vila Velha": [-20.3297, -40.2925],
    },
    GO: {
        Goiânia: [-16.6869, -49.2648],
        "Aparecida de Goiânia": [-16.8239, -49.2439],
    },
    MA: {
        "São Luís": [-2.5387, -44.2825],
        Imperatriz: [-5.5264, -47.4919],
    },
    MT: {
        Cuiabá: [-15.6014, -56.0979],
        "Várzea Grande": [-15.6461, -56.1326],
    },
    MS: {
        "Campo Grande": [-20.4697, -54.6201],
        Dourados: [-22.2211, -54.8056],
    },
    MG: {
        "Belo Horizonte": [-19.9167, -43.9345],
        Uberlândia: [-18.9113, -48.2622],
        Contagem: [-19.9317, -44.0536],
    },
    PA: {
        Belém: [-1.4558, -48.5044],
        Ananindeua: [-1.3656, -48.3722],
    },
    PB: {
        "João Pessoa": [-7.1195, -34.845],
        "Campina Grande": [-7.2306, -35.8811],
    },
    PR: {
        Curitiba: [-25.4284, -49.2733],
        Londrina: [-23.3045, -51.1696],
        Maringá: [-23.4205, -51.9331],
    },
    PE: {
        Recife: [-8.0476, -34.877],
        "Jaboatão dos Guararapes": [-8.112, -35.0149],
    },
    PI: {
        Teresina: [-5.0892, -42.8019],
        Parnaíba: [-2.9058, -41.7767],
    },
    RJ: {
        "Rio de Janeiro": [-22.9068, -43.1729],
        "São Gonçalo": [-22.8267, -43.0537],
        "Duque de Caxias": [-22.7856, -43.3117],
    },
    RN: {
        Natal: [-5.7945, -35.211],
        Mossoró: [-5.1875, -37.3444],
    },
    RS: {
        "Porto Alegre": [-30.0346, -51.2177],
        "Caxias do Sul": [-29.1634, -51.1797],
        Pelotas: [-31.7654, -52.3376],
    },
    RO: {
        "Porto Velho": [-8.7612, -63.9039],
        "Ji-Paraná": [-10.8756, -61.9378],
    },
    RR: {
        "Boa Vista": [2.8235, -60.6758],
    },
    SC: {
        Florianópolis: [-27.5954, -48.548],
        Joinville: [-26.3044, -48.8487],
        Blumenau: [-26.9194, -49.0661],
    },
    SP: {
        "São Paulo": [-23.5505, -46.6333],
        Guarulhos: [-23.4538, -46.5333],
        Campinas: [-22.9099, -47.0626],
        "São Bernardo do Campo": [-23.6914, -46.5646],
    },
    SE: {
        Aracaju: [-10.9472, -37.0731],
        "Nossa Senhora do Socorro": [-10.8551, -37.1264],
    },
    TO: {
        Palmas: [-10.1689, -48.3317],
        Araguaína: [-7.1911, -48.2072],
    },
};

// Library themes
const libraryThemes = [
    "Literatura Clássica",
    "Ficção Científica",
    "Romance",
    "Mistério e Suspense",
    "História",
    "Biografia",
    "Autoajuda",
    "Tecnologia",
    "Ciências",
    "Arte e Cultura",
    "Infantil",
    "Juvenil",
    "Poesia",
    "Filosofia",
    "Religião",
    "Culinária",
    "Saúde e Bem-estar",
    "Negócios",
    "Educação",
    "Viagem",
];

// Sample libraries data
const libraries = [
    {
        name: "Biblioteca Central de São Paulo",
        lat: -23.5505,
        lng: -46.6333,
        theme: "Literatura Clássica",
        state: "SP",
        city: "São Paulo",
        address: "Rua da Consolação, 94 - Centro, São Paulo - SP",
    },
    {
        name: "Biblioteca Nacional do Rio de Janeiro",
        lat: -22.9068,
        lng: -43.1729,
        theme: "História",
        state: "RJ",
        city: "Rio de Janeiro",
        address: "Av. Rio Branco, 219 - Centro, Rio de Janeiro - RJ",
    },
    {
        name: "Biblioteca Pública de Belo Horizonte",
        lat: -19.9167,
        lng: -43.9345,
        theme: "Arte e Cultura",
        state: "MG",
        city: "Belo Horizonte",
        address: "Praça da Liberdade, s/n - Funcionários, Belo Horizonte - MG",
    },
    {
        name: "Biblioteca Central de Brasília",
        lat: -15.7942,
        lng: -47.8822,
        theme: "Ciências",
        state: "DF",
        city: "Brasília",
        address:
            "Campus Universitário Darcy Ribeiro - Asa Norte, Brasília - DF",
    },
    {
        name: "Biblioteca Pública do Paraná",
        lat: -25.4284,
        lng: -49.2733,
        theme: "Literatura Clássica",
        state: "PR",
        city: "Curitiba",
        address: "Rua Cândido Lopes, 133 - Centro, Curitiba - PR",
    },
];

// Initialize map
function initMap() {
    // Show loading overlay
    document.querySelector(".loading-overlay").style.display = "flex";

    // Initialize map centered on Brazil
    map = L.map("map").setView([-14.235, -51.9253], 4);

    // Add tile layer
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "© OpenStreetMap contributors",
    }).addTo(map);

    // Try to get user location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };

                // Add user marker
                userMarker = L.marker([userLocation.lat, userLocation.lng], {
                    icon: userIcon,
                })
                    .addTo(map)
                    .bindPopup("Sua localização")
                    .openPopup();

                // Center map on user location
                map.setView([userLocation.lat, userLocation.lng], 10);

                // Create libraries after getting user location
                createLibraries();
            },
            function (error) {
                console.log("Geolocation error:", error);
                // Create libraries without user location
                createLibraries();
            }
        );
    } else {
        console.log("Geolocation not supported");
        createLibraries();
    }
}

// Create library markers
function createLibraries() {
    libraries.forEach(function (library) {
        const marker = L.marker([library.lat, library.lng], {
            icon: libraryIcon,
        }).addTo(map).bindPopup(`
                <div style="min-width: 200px;">
                    <h4 style="margin: 0 0 8px 0; color: #28405a;">${
                        library.name
                    }</h4>
                    <p style="margin: 4px 0; font-size: 0.9em;"><strong>Tema:</strong> ${
                        library.theme
                    }</p>
                    <p style="margin: 4px 0; font-size: 0.9em;"><strong>Endereço:</strong> ${
                        library.address
                    }</p>
                    ${
                        userLocation
                            ? `<p style="margin: 4px 0; font-size: 0.9em;"><strong>Distância:</strong> ${calculateDistance(
                                  userLocation.lat,
                                  userLocation.lng,
                                  library.lat,
                                  library.lng
                              ).toFixed(1)} km</p>`
                            : ""
                    }
                </div>
            `);

        libraryMarkers.push({
            marker: marker,
            data: library,
        });
    });

    // Hide loading overlay
    document.querySelector(".loading-overlay").style.display = "none";
}

// Update cities based on selected state
function updateCities() {
    const stateSelect = document.getElementById("estado");
    const citySelect = document.getElementById("cidade");
    const selectedState = stateSelect.value;

    // Clear city options
    citySelect.innerHTML = '<option value="">Todas as cidades</option>';

    if (selectedState && cityCoordinates[selectedState]) {
        Object.keys(cityCoordinates[selectedState]).forEach(function (city) {
            const option = document.createElement("option");
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
    }
}

// Center map on selected city
function centerOnCity() {
    const stateSelect = document.getElementById("estado");
    const citySelect = document.getElementById("cidade");
    const selectedState = stateSelect.value;
    const selectedCity = citySelect.value;

    if (
        selectedState &&
        selectedCity &&
        cityCoordinates[selectedState] &&
        cityCoordinates[selectedState][selectedCity]
    ) {
        const coords = cityCoordinates[selectedState][selectedCity];
        map.setView(coords, 12);
    } else if (selectedState && stateCoordinates[selectedState]) {
        const coords = stateCoordinates[selectedState];
        map.setView(coords, 8);
    }
}

// Filter libraries
function filterLibraries() {
    const theme = document.getElementById("tema").value.toLowerCase();
    const state = document.getElementById("estado").value;
    const city = document.getElementById("cidade").value;
    const maxDistance = parseFloat(document.getElementById("distancia").value);

    libraryMarkers.forEach(function (item) {
        let show = true;

        // Filter by theme
        if (theme && !item.data.theme.toLowerCase().includes(theme)) {
            show = false;
        }

        // Filter by state
        if (state && item.data.state !== state) {
            show = false;
        }

        // Filter by city
        if (city && item.data.city !== city) {
            show = false;
        }

        // Filter by distance
        if (maxDistance && userLocation) {
            const distance = calculateDistance(
                userLocation.lat,
                userLocation.lng,
                item.data.lat,
                item.data.lng
            );
            if (distance > maxDistance) {
                show = false;
            }
        }

        // Show/hide marker
        if (show) {
            item.marker.addTo(map);
        } else {
            map.removeLayer(item.marker);
        }
    });
}

// Calculate distance between two points (Haversine formula)
function calculateDistance(lat1, lng1, lat2, lng2) {
    const R = 6371; // Earth's radius in kilometers
    const dLat = ((lat2 - lat1) * Math.PI) / 180;
    const dLng = ((lng2 - lng1) * Math.PI) / 180;
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos((lat1 * Math.PI) / 180) *
            Math.cos((lat2 * Math.PI) / 180) *
            Math.sin(dLng / 2) *
            Math.sin(dLng / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    // Initialize map
    initMap();

    // Populate theme select
    const themeSelect = document.getElementById("tema");
    libraryThemes.forEach(function (theme) {
        const option = document.createElement("option");
        option.value = theme;
        option.textContent = theme;
        themeSelect.appendChild(option);
    });

    // Add event listeners
    document.getElementById("estado").addEventListener("change", function () {
        updateCities();
        centerOnCity();
        filterLibraries();
    });

    document.getElementById("cidade").addEventListener("change", function () {
        centerOnCity();
        filterLibraries();
    });

    document.getElementById("tema").addEventListener("change", filterLibraries);
    document
        .getElementById("distancia")
        .addEventListener("input", filterLibraries);

    // Add keyboard event listener for Enter key
    document.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            filterLibraries();
        }
    });
});

// Export functions for global access if needed
window.updateCities = updateCities;
window.centerOnCity = centerOnCity;
window.filterLibraries = filterLibraries;
