document.addEventListener('DOMContentLoaded', () => {
    const charts = {};
    const rootStyles = getComputedStyle(document.documentElement);
    const navyColor = rootStyles.getPropertyValue('--navy').trim() || '#334155';
    const seuils = window.seuilsGraphiques || {};
    const initialData = window.donneesCapteurs || {};
    const capteursActifs = window.dispositifsCapteurs || [];

    console.log('Données des capteurs:', initialData);
    console.log('Capteurs actifs:', capteursActifs);

    // Retourne l'unité en fonction du nom de la table
    function getUniteFromTable(nomTable) {
        const unites = {
            'capteur_temp_hum': ['°C', '%'],
            'CapteurSon': 'dB',
            'CapteurLumiere': 'lux',
            'CapteurProximite': 'cm',
            'CapteurGaz': 'ppm'
        };
        return unites[nomTable] || '';
    }

    // Retourne la couleur en fonction du type de capteur
    function getCouleurCapteur(nomTable, index = 0) {
        const couleurs = {
            'capteur_temp_hum': ['#ef4444', '#3b82f6'], // Rouge pour température, bleu pour humidité
            'CapteurSon': '#f59e0b',
            'CapteurLumiere': '#eab308',
            'CapteurProximite': '#10b981',
            'CapteurGaz': '#8b5cf6'
        };

        const couleur = couleurs[nomTable];
        if (Array.isArray(couleur)) {
            return couleur[index] || couleur[0];
        }
        return couleur || navyColor;
    }

    // Normalise et valide une valeur temporelle - VERSION CORRIGÉE
    function normalizeTimeData(timeValue) {
        // Rejeter les valeurs invalides ou nulles
        if (timeValue === null || timeValue === undefined || timeValue === '' || timeValue === 0 || timeValue === '0') {
            return null;
        }

        let timestamp = null;
        const currentTime = Date.now();
        const minValidTime = new Date('2020-01-01').getTime(); // Temps minimum acceptable
        const maxValidTime = currentTime + (365 * 24 * 60 * 60 * 1000); // +1 an dans le futur

        if (typeof timeValue === 'number') {
            // Si en secondes (timestamp UNIX), convertir en millisecondes
            if (timeValue < 4102444800) { // moins que 2100 en secondes
                timestamp = timeValue * 1000;
            } else {
                timestamp = timeValue;
            }
        } else if (typeof timeValue === 'string') {
            // Essayer d'abord format ISO (remplacer espace par T)
            let dateISO = new Date(timeValue.replace(' ', 'T'));
            if (!isNaN(dateISO.getTime())) {
                timestamp = dateISO.getTime();
            } else {
                // Sinon, parse classique
                let dateClassic = new Date(timeValue);
                if (!isNaN(dateClassic.getTime())) {
                    timestamp = dateClassic.getTime();
                }
            }
        }

        // Validation stricte du timestamp
        if (timestamp === null || timestamp <= 0 || isNaN(timestamp)) {
            return null;
        }

        // Vérifier que le timestamp est dans une plage raisonnable
        if (timestamp < minValidTime || timestamp > maxValidTime) {
            console.warn(`Timestamp hors plage valide: ${timestamp} (${new Date(timestamp)})`);
            return null;
        }

        const date = new Date(timestamp);
        if (isNaN(date.getTime())) {
            return null;
        }

        return date;
    }

    // Filtre, normalise et trie les données temporelles - VERSION AMÉLIORÉE
    function validateTimeData(data) {
        const validData = data
            .map(d => ({ 
                ...d, 
                normalizedTime: normalizeTimeData(d.temps),
                originalTime: d.temps // Garder la valeur originale pour debug
            }))
            .filter(d => {
                if (d.normalizedTime === null) {
                    console.warn('Données temporelles invalides:', d.originalTime);
                    return false;
                }
                return true;
            })
            .sort((a, b) => a.normalizedTime - b.normalizedTime);

        // Log pour debug
        if (validData.length > 0) {
            console.log('Plage temporelle des données:', {
                debut: validData[0].normalizedTime,
                fin: validData[validData.length - 1].normalizedTime,
                total: validData.length
            });
        }

        return validData;
    }

    // Détermine l'unité de temps à utiliser sur l'axe X - VERSION AMÉLIORÉE
    function getTimeUnit(dataPoints) {
        if (dataPoints.length < 2) return 'hour';

        const firstTime = dataPoints[0].normalizedTime;
        const lastTime = dataPoints[dataPoints.length - 1].normalizedTime;
        const timeSpan = lastTime - firstTime;

        const oneMinute = 60 * 1000;
        const oneHour = 60 * oneMinute;
        const oneDay = 24 * oneHour;
        const oneWeek = 7 * oneDay;
        const oneMonth = 30 * oneDay;

        console.log(`Durée des données: ${timeSpan}ms (${timeSpan / oneHour}h)`);

        if (timeSpan <= 2 * oneHour) return 'minute';
        if (timeSpan <= 2 * oneDay) return 'hour';
        if (timeSpan <= 2 * oneWeek) return 'day';
        if (timeSpan <= 2 * oneMonth) return 'week';
        return 'month';
    }

    capteursActifs.forEach(capteur => {
        const canvasElement = document.getElementById(`chart-${capteur.id}`);
        if (!canvasElement) {
            console.warn(`Canvas non trouvé pour le capteur ${capteur.id}`);
            return;
        }

        const data = initialData[capteur.id];
        if (!data || !data.history || data.history.length === 0) {
            console.warn(`Pas d'historique ou données manquantes pour le capteur ${capteur.id}`);
            return;
        }

        console.log(`Traitement du capteur ${capteur.id} (${capteur.nom_table_bdd}):`, data);

        const ctx = canvasElement.getContext('2d');
        const nomTable = capteur.nom_table_bdd;
        const isTempHum = nomTable === 'capteur_temp_hum';

        // Nettoyage et validation des données temporelles
        const validatedHistory = validateTimeData(data.history);
        if (validatedHistory.length === 0) {
            console.warn(`Aucune donnée temporelle valide pour le capteur ${capteur.id}`);
            return;
        }

        const timeUnit = getTimeUnit(validatedHistory);

        let datasets = [];

        if (isTempHum) {
            const tempData = validatedHistory
                .filter(d => d.temperature != null && !isNaN(parseFloat(d.temperature)))
                .map(d => ({
                    x: d.normalizedTime,
                    y: parseFloat(d.temperature)
                }));

            const humData = validatedHistory
                .filter(d => d.humidite != null && !isNaN(parseFloat(d.humidite)))
                .map(d => ({
                    x: d.normalizedTime,
                    y: parseFloat(d.humidite)
                }));

            if (tempData.length > 0) {
                datasets.push({
                    label: 'Température (°C)',
                    data: tempData,
                    borderColor: getCouleurCapteur(nomTable, 0),
                    backgroundColor: getCouleurCapteur(nomTable, 0) + '20',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    yAxisID: 'y'
                });
            }

            if (humData.length > 0) {
                datasets.push({
                    label: 'Humidité (%)',
                    data: humData,
                    borderColor: getCouleurCapteur(nomTable, 1),
                    backgroundColor: getCouleurCapteur(nomTable, 1) + '20',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    yAxisID: 'y1'
                });
            }
        } else {
            const unite = getUniteFromTable(nomTable);
            const labelCapteur = capteur.nom || 'Valeur';

            const chartData = validatedHistory
                .filter(d => d.valeur != null && !isNaN(parseFloat(d.valeur)))
                .map(d => ({
                    x: d.normalizedTime,
                    y: parseFloat(d.valeur)
                }));

            if (chartData.length > 0) {
                datasets.push({
                    label: `${labelCapteur} ${unite ? `(${unite})` : ''}`,
                    data: chartData,
                    borderColor: getCouleurCapteur(nomTable),
                    backgroundColor: getCouleurCapteur(nomTable) + '20',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0
                });
            }
        }

        // Vérifier qu'on a au moins un dataset avec des données
        if (datasets.length === 0 || datasets.every(ds => ds.data.length === 0)) {
            console.warn(`Aucune donnée valide pour afficher le graphique du capteur ${capteur.id}`);
            return;
        }

        // Configuration des échelles - VERSION CORRIGÉE
        const scalesConfig = {
            x: {
                type: 'time',
                time: {
                    unit: timeUnit,
                    tooltipFormat: 'dd/MM/yyyy HH:mm',
                    displayFormats: {
                        minute: 'HH:mm',
                        hour: 'HH:mm',
                        day: 'dd/MM',
                        week: 'dd/MM',
                        month: 'MM/yyyy'
                    }
                },
                ticks: {
                    display: true,
                    maxTicksLimit: 8,
                    color: '#64748b',
                    source: 'auto',
                    // Forcer Chart.js à utiliser les données réelles, pas les limites par défaut
                    autoSkip: true,
                    autoSkipPadding: 15
                },
                grid: { display: false },
                // Configuration critique pour éviter l'erreur
                min: function(context) {
                    // Utiliser le minimum des données réelles
                    const allData = context.chart.data.datasets.flatMap(ds => ds.data);
                    if (allData.length > 0) {
                        return Math.min(...allData.map(d => d.x.getTime ? d.x.getTime() : d.x));
                    }
                    return undefined;
                },
                max: function(context) {
                    // Utiliser le maximum des données réelles
                    const allData = context.chart.data.datasets.flatMap(ds => ds.data);
                    if (allData.length > 0) {
                        return Math.max(...allData.map(d => d.x.getTime ? d.x.getTime() : d.x));
                    }
                    return undefined;
                }
            },
            y: {
                beginAtZero: false,
                grid: { color: '#e2e8f0' },
                ticks: { color: '#334155' },
                position: 'left',
                display: true
            }
        };

        if (isTempHum) {
            scalesConfig.y1 = {
                beginAtZero: false,
                grid: { display: false },
                ticks: { color: '#334155' },
                position: 'right',
                display: true
            };
        }

        // Annotations pour seuils
        const seuilsCapteur = seuils[capteur.id] || {};
        const annotations = [];

        if (seuilsCapteur.seuilSup != null) {
            annotations.push({
                type: 'line',
                yMin: seuilsCapteur.seuilSup,
                yMax: seuilsCapteur.seuilSup,
                borderColor: 'red',
                borderWidth: 1,
                label: {
                    enabled: true,
                    content: `Seuil supérieur (${seuilsCapteur.seuilSup})`,
                    position: 'end',
                    backgroundColor: 'red',
                    color: 'white'
                }
            });
        }

        if (seuilsCapteur.seuilInf != null) {
            annotations.push({
                type: 'line',
                yMin: seuilsCapteur.seuilInf,
                yMax: seuilsCapteur.seuilInf,
                borderColor: 'blue',
                borderWidth: 1,
                label: {
                    enabled: true,
                    content: `Seuil inférieur (${seuilsCapteur.seuilInf})`,
                    position: 'start',
                    backgroundColor: 'blue',
                    color: 'white'
                }
            });
        }

        // Supprimer le graphique précédent s'il existe
        if (charts[capteur.id]) {
            charts[capteur.id].destroy();
        }

        // Création du graphique Chart.js
        try {
            charts[capteur.id] = new Chart(ctx, {
                type: 'line',
                data: { datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    parsing: false,
                    normalized: true,
                    scales: scalesConfig,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { color: navyColor }
                        },
                        tooltip: {
                            mode: 'nearest',
                            intersect: false,
                            position: 'nearest',
                            backgroundColor: navyColor,
                            titleColor: 'white',
                            bodyColor: 'white',
                            callbacks: {
                                label: ctx => {
                                    const label = ctx.dataset.label || '';
                                    const yVal = ctx.parsed.y;
                                    return `${label}: ${yVal}`;
                                }
                            }
                        },
                        annotation: {
                            annotations
                        }
                    }
                }
            });
            
            console.log(`Graphique créé avec succès pour le capteur ${capteur.id}`);
        } catch (error) {
            console.error(`Erreur lors de la création du graphique pour le capteur ${capteur.id}:`, error);
        }
    });
});