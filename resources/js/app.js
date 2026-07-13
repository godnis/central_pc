import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

const CATEGORIAS_DEPENDENTES_DA_PLACA_MAE = ['ram', 'armazenamento', 'gabinete'];

Alpine.data('maquinaComponentesForm', (config) => ({
    compativeisUrl: config.compativeisUrl,
    opcoes: { ...config.componentesIniciais },
    selecionados: config.selecionados,
    avisoFonte: null,
    carregando: false,

    init() {
        ['cpu', 'placa_mae', 'ram', 'armazenamento', 'gpu', 'fonte', 'gabinete'].forEach((categoria) => {
            if (!this.opcoes[categoria]) {
                this.opcoes[categoria] = [];
            }
        });

        this.atualizarOpcoes('placa_mae');
        this.atualizarOpcoes('cpu');
        CATEGORIAS_DEPENDENTES_DA_PLACA_MAE.forEach((categoria) => this.atualizarOpcoes(categoria));
        this.atualizarAvisoFonte();
    },

    payloadSelecionados() {
        return {
            cpu: this.selecionados.cpu ? [this.selecionados.cpu] : [],
            placa_mae: this.selecionados.placa_mae ? [this.selecionados.placa_mae] : [],
            ram: this.selecionados.ram.map((item) => item.componente_id).filter(Boolean),
            armazenamento: this.selecionados.armazenamento.map((item) => item.componente_id).filter(Boolean),
            gpu: this.selecionados.gpu ? [this.selecionados.gpu] : [],
            fonte: this.selecionados.fonte ? [this.selecionados.fonte] : [],
            gabinete: this.selecionados.gabinete ? [this.selecionados.gabinete] : [],
        };
    },

    async atualizarOpcoes(categoriaAlvo) {
        this.carregando = true;
        try {
            const { data } = await window.axios.post(this.compativeisUrl, {
                categoria_alvo: categoriaAlvo,
                selecionados: this.payloadSelecionados(),
            });
            this.opcoes[categoriaAlvo] = data.componentes;
        } finally {
            this.carregando = false;
        }
    },

    async atualizarAvisoFonte() {
        if (!this.selecionados.fonte) {
            this.avisoFonte = null;
            return;
        }

        const { data } = await window.axios.post(this.compativeisUrl, {
            categoria_alvo: 'fonte',
            selecionados: this.payloadSelecionados(),
        });
        this.avisoFonte = data.aviso_fonte;
    },

    async onCpuChange() {
        this.selecionados.placa_mae = null;
        await this.atualizarOpcoes('placa_mae');
        await this.atualizarAvisoFonte();
    },

    async onPlacaMaeChange() {
        this.selecionados.ram = [];
        this.selecionados.armazenamento = [];
        this.selecionados.gabinete = null;
        await Promise.all(CATEGORIAS_DEPENDENTES_DA_PLACA_MAE.map((categoria) => this.atualizarOpcoes(categoria)));
    },

    async onGpuChange() {
        await this.atualizarAvisoFonte();
    },

    async onFonteChange() {
        await this.atualizarAvisoFonte();
    },

    adicionarRam() {
        const primeiro = this.opcoes.ram[0];
        this.selecionados.ram.push({ componente_id: primeiro ? primeiro.id : '', quantidade: 1 });
    },

    removerRam(index) {
        this.selecionados.ram.splice(index, 1);
    },

    adicionarArmazenamento() {
        const primeiro = this.opcoes.armazenamento[0];
        this.selecionados.armazenamento.push({ componente_id: primeiro ? primeiro.id : '', quantidade: 1 });
    },

    removerArmazenamento(index) {
        this.selecionados.armazenamento.splice(index, 1);
    },
}));

Alpine.start();
