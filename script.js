const candidatos = {
  '30': { nome: 'Arthur Anael', partido: 'Educação Fisica', foto: 'imagens/arthur.jpg', cargo: 'Representante de Turma' },
  '26': { nome: 'Silvio Santos', partido: 'Tecnologia', foto: 'imagens/silvio.png', cargo: 'Representante de Turma' },
  '69666': { nome: 'Guilherme', partido: 'Direito', foto: 'imagens/guilherme.jpg', cargo: 'Representante de Turma' },
  '26132': { nome: 'Maria Joaquina', partido: 'Saúde', foto: 'imagens/maria.png', cargo: 'Representante de Turma' }
};

let cargoAtual = 'representante de turma';
let votos = { prefeito: null, vereador: null };

function digitar(numero) {
  const input = document.getElementById('numero');
  const somBip = document.getElementById('som-bip');
  const maxLength = cargoAtual === 'prefeito' ? 2 : 5;

  if (input.value.length < maxLength) {
      input.value += numero;
      somBip.play(); // Toca o som do bip
      atualizarCandidato(input.value);
  }
}

function atualizarCandidato(numero) {
  const nomeCandidato = document.getElementById('nome-candidato');
  const partidoCandidato = document.getElementById('partido-candidato');
  const fotoCandidato = document.getElementById('foto-candidato');

  if (candidatos[numero] && candidatos[numero].cargo.toLowerCase() === cargoAtual) {
      nomeCandidato.textContent = `Nome: ${candidatos[numero].nome}`;
      partidoCandidato.textContent = `Partido: ${candidatos[numero].partido}`;
      fotoCandidato.src = candidatos[numero].foto;
      fotoCandidato.style.display = 'block';
  } else {
      nomeCandidato.textContent = 'Voto Nulo';
      partidoCandidato.textContent = '';
      fotoCandidato.style.display = 'none';
  }
}

function votoBranco() {
  document.getElementById('numero').value = 'Branco'; // Define o campo como 'Branco'
  confirmarVoto(); // Chama a função para confirmar o voto
}

function corrigir() {
  document.getElementById('numero').value = '';
  document.getElementById('nome-candidato').textContent = '';
  document.getElementById('partido-candidato').textContent = '';
  document.getElementById('foto-candidato').style.display = 'none';
}

function confirmar() {
  const numero = document.getElementById('numero').value;
  votos[cargoAtual] = numero || 'Branco';
  corrigir();

  if (cargoAtual === 'prefeito') {
      cargoAtual = 'vereador';
      document.getElementById('cargo-atual').textContent = 'Vereador';
      document.getElementById('numero').maxLength = 5;
  } else {
      mostrarFim();
  }
}

function mostrarFim() {
  const somFinalizacao = document.getElementById('som-finalizacao');
  somFinalizacao.play(); // Toca o som de finalização

  // Esconde todos os outros elementos e exibe apenas "FIM"
  document.querySelector('.urna').innerHTML = `
    <div id="tela-fim" class="tela-fim" style="display: flex;">
      <p>FIM</p>
    </div>
  `;

  // Redireciona para a página de resultados após 3 segundos
  setTimeout(() => {
      window.location.href = "resultado.html";
  }, 3000); // Ajuste o tempo de espera aqui, em milissegundos (3000ms = 3 segundos)
}


function confirmarVoto() {
  const numero = document.getElementById("numero").value;
  const cargoAtual = document.getElementById("cargo-atual").textContent.toLowerCase();

  fetch('processa_voto.php', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: `id_candidato=${encodeURIComponent(numero)}&cargo_votado=${encodeURIComponent(cargoAtual)}`
  })
  .then(response => response.text())
  .then(data => {
      console.log("Resposta do PHP:", data);
      document.getElementById("numero").value = ""; // Limpa o campo de entrada

      // Determina o tipo de voto e o registra
      if (numero.toLowerCase() === 'branco') {
          incrementarVoto('branco', cargoAtual);
      } else if (!['30', '26', '69666', '26132'].includes(numero)) {
          incrementarVoto('nulo', cargoAtual);
      } else {
          incrementarVoto('valido', cargoAtual, numero);
      }

      confirmar();  // Avança para o próximo cargo
  })
  .catch(error => console.error('Erro:', error));
}

// Função para incrementar os votos no sessionStorage
function incrementarVoto(tipo, cargo, numero = null) {
  const chave = `${tipo}_${cargo}`; // Gera a chave, ex: 'valido_prefeito', 'nulo_vereador'
  let contagem = parseInt(sessionStorage.getItem(chave)) || 0;
  sessionStorage.setItem(chave, contagem + 1);

  // Armazena o número do candidato, se for um voto válido
  if (tipo === 'valido' && numero) {
      sessionStorage.setItem(`candidato_${cargo}`, numero);
  }
}

// Selecionar botão dropdown e conteúdo
const dropdownBtn = document.querySelector('.dropdown-btn');
const dropdownContent = document.querySelector('.dropdown-content');

// Adicionar evento de clique para exibir ou ocultar o menu
dropdownBtn.addEventListener('click', () => {
    if (dropdownContent.style.display === "block") {
        dropdownContent.style.display = "none";
    } else {
        dropdownContent.style.display = "block";
    }
});

