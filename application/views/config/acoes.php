<div class="col-md-4 col-lg-3">
        <form action="/transacoes/sincronizar" method="post">
                <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>">
                <div class="input-group mb-3">
                        <span class="input-group-text" />Dias</span> <input type="number" value="7" name="dias" class="form-control">
                        <button type="submit" class="btn btn-primary">Sincronizar Transações Recentes</button>
                </div>
        </form>

        <form action="/transacoes/sincronizaTransacoesVencidas" method="post">
                <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>">
                <div class="input-group mb-3">
                        <button type="submit" class="btn btn-primary">Sincronizar Transações Vencidas</button>
                </div>
        </form>

        <form action="/inscricoes/totalizar" method="post">
                <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>">
                <div class="input-group mb-3">
                        <button type="submit" class="btn btn-primary">Totalizar Inscrições de Grupos Ativos</button>
                </div>
        </form>
</div>