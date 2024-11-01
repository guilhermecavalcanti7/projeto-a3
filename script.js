const candidatos = {
    '69': { nome: 'Arthur Anael', partido: 'Linkin Park', foto: 'imagens/arthur.jpg', cargo: 'Prefeito' },
    '26': { nome: 'Silvio Santos', partido: 'Carrossel', foto: 'imagens/silvio.jpg', cargo: 'Prefeito' },
    '69666': { nome: 'Guilherme', partido: 'Linkin Park', foto: 'imagens/guilherme.jpg', cargo: 'Vereador' },
    '26132': { nome: 'Maria Joaquina', partido: 'Carrossel', foto: 'imagens/maria.jpg', cargo: 'Vereador' }
  };
  
  let cargoAtual = 'prefeito';
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
    votos[cargoAtual] = 'Branco';
    confirmar();
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
  
    document.getElementById('tela-votacao').style.display = 'none';
    document.getElementById('tela-fim').style.display = 'flex';
  }
  
  function confirmarVoto() {
    const numero = document.getElementById("numero").value;
    const cargoAtual = "Prefeito"; // ajuste conforme necessário

    fetch('processa_voto.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id_candidato=${encodeURIComponent(numero)}&cargo_votado=${encodeURIComponent(cargoAtual)}`
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        alert("Voto confirmado!");
        // Redireciona ou limpa o campo após o voto
        document.getElementById("numero").value = "";
    })
    .catch(error => console.error('Erro:', error));
}
