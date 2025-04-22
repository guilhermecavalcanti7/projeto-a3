async function carregarResultados() {
    const dataAtual = new Date();
    document.getElementById('horaAtualizacao').textContent = dataAtual.toLocaleString();

    try {
        const response = await fetch('obter_resultados.php');
        
        if (!response.ok) {
            throw new Error("Erro ao buscar os dados de resultados: " + response.statusText);
        }
        
        const resultados = await response.json();

        // Só preenche uma vez
        preencherCandidatos(resultados.prefeito, 'prefeito-container');

        criarGraficoPizza(resultados);
    } catch (error) {
        console.error("Erro ao carregar resultados:", error);
        alert('Erro ao carregar os resultados. Verifique a conexão e tente novamente.');
    }
}

function preencherCandidatos(candidatos, containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';

    candidatos.forEach(candidato => {
        const candidatoDiv = document.createElement('div');
        candidatoDiv.classList.add('resultado-candidato');
        candidatoDiv.innerHTML = `
            <img src="${candidato.foto}" alt="Foto de ${candidato.nome}" class="foto-candidato">
            <div class="info-candidato">
                <h3>${candidato.nome}</h3>
                <p class="partido">${candidato.partido}</p>
                <div class="votos">
                    <span class="percentual">${candidato.percentual}%</span>
                    <span class="quantidade">${candidato.votos} votos</span>
                </div>
                <span class="status ${candidato.eleito ? 'eleito' : 'nao-eleito'}">
                    ${candidato.eleito ? 'Eleito' : 'Não Eleito'}
                </span>
            </div>
        `;
        container.appendChild(candidatoDiv);
    });
}

function criarGraficoPizza(resultados) {
    const ctx = document.getElementById('graficoResultados').getContext('2d');

    if (!resultados.prefeito || resultados.prefeito.length === 0) {
        console.error("Não há dados suficientes para criar o gráfico.");
        return;
    }

    const candidatos = resultados.prefeito.map(candidato => candidato.nome);
    const votos = resultados.prefeito.map(candidato => candidato.votos);

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: candidatos,
            datasets: [{
                label: 'Votos para Representante de Turma',
                data: votos,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

window.onload = carregarResultados;
