# Hackathon Discord Bot

Aquela ferramenta essencial pra você organizar qualquer Hackathon na maior tranquilidade dentro do Discord!

## Motivação

Nos últimos dias após o desastre no Rio Grande do Sul muitas propostas de Hackathons tem sido criadas e a necessidade de
uma organização ficou bem clara. Nosso papel é fortalecer a comunidade para que consigamos implementar tudo que um
evento
precise sem muita dor de cabeça.

## Stack Utilizada

O bom e velho PHP com Laravel OOP vem sempre fazendo magia nesses momentos :p

| Tecnologia | Versão |
|------------|--------|
| PHP        | ^8.2   |
| Laravel    | ^11.x  |
| DiscordPHP | ^7.3   |

## Comandos Planejados

Esses são os comandos do MVP! Mas caso você tenha ideia do que implementar, abra
uma [issue](https://github.com/TechPeloRS/discord-bot/issues/new) e vamos discutir!

|     | Comando       | Argumentos                           | Descrição                                                     |
|-----|---------------|--------------------------------------|---------------------------------------------------------------|
| [x] | /ping         | N/D                                  | Apenas aquele teste pra ver se tá online.                     |
| [x] | /entrar-time  | `chave`, `area-de-atuacao`, `github` | Entre no seu time utilizando a chave providenciada pelo lider |
| [ ] | /time         | `atualizar/sair`                     | Gerenciamento de times                                        |
| [ ] | /estatisticas | N/D                                  | Estatisticas Gerais do Hackathon                              |
| [ ] | /ajuda        | `mentoria/moderacao` `mensagem`      | Envia um pedido de ajuda para a sala dos mentores.                  |

## Estrutura do Projeto

Estamos utilizando o Artisan para criar o comando inicial, onde ele inicia o Event Loop com o Discord e define quais
serão os eventos que serão escutados.

`````php
class StartServer extends Command
{
    protected $signature = 'bot:start';
    
    protected $description = 'Command description';

    public function handle(
        MessageCreate $messageCreate,
        SlashCommands $slashCommands,
    ): int
    {
        $discord = new Discord([
            'token' => config('services.discord.token'),
            'intents' => Intents::getDefaultIntents(),
        ]);

        $discord->on('ready', function (Discord $discord) use ($messageCreate, $slashCommands){
            $discord->on(Event::MESSAGE_CREATE, fn ($message) => $messageCreate->handle($message, $discord));
            $slashCommands->setup($discord);
        });

        $discord->run();

        return self::SUCCESS;
    }
}
`````

### Criando Comandos

Uma abstração para a implementação de tudo referente ao Discord foi feita em `app/Discord`, onde é separado em duas
estruturas:

- Commands: Onde ficam os comandos executados pelo usuário do Discord.
- Events: Onde ficam os eventos que serão escutados pelo bot.

Para novos comandos, estamos simplesmente "adicionando" algo novo sem alterar o que já está escrito, seguindo o
princípio [Open Closed do SOLID](https://github.com/danielhe4rt/solid4noobs). E isso você pode fazer diretamente em 
`app/Discord/Commands/CommandsEnum.php`.

`````php
enum CommandsEnum: string
{
    case Ping = 'ping';
    case SetupServer = 'test'; // novo comando

    public function getDescription(): string
    {
        return match ($this) {
            self::Ping => 'Replies with Pong!',
            // nova descrição
        };
    }

    public function getPermissions(): int
    {
        return match ($this) {
            self::Ping, self::JoinTeam => (1 << 11),
            // nova permissão
        };
    }

    public function getAction(): CommandInterface
    {
        return match ($this) {
            self::Ping => app(PingCommand::class),
            // nova ação
        };
    }

    public function getOptions(): array
    {
        return match ($this) {
            self::Ping => [
                [
                    'name' => 'test',
                    'description' => 'Test Option',
                    'type' => Command::MESSAGE,
                    'required' => true
                ]
            ],
            // argumentos pro seu comando
        };
    }
}
`````

A partir daí, basta seguir a estrutura proposta pela pasta Commands e vai estar tudo certo!

## Autores

- **Daniel Reis (danielhe4rt)** - _ScyllaDB Developer Advocate_ - [Twitter](https://twitter.com/danielhe4rt)

## Contribuição

Este projeto segue a especificação [all-contributors](https://github.com/all-contributors/all-contributors).
Contribuições de qualquer tipo são bem-vindas!

