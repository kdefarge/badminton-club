import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ["result"];
    static values = {
        encounters: Array
    };

    initialize() {
        this.affinity = new Array();
        console.log(this.count);
    }

    generate() {
        for(const encounter of this.encountersValue) {
            for(const encounterPlayer of encounter.encounterPlayers) {
                var player1Id = encounterPlayer.player.id;
                if(this.affinity[player1Id] === undefined) {
                    this.affinity[player1Id] = {
                        together: new Array(),
                        against: new Array(),
                        played: 0,
                        difference: 0
                    };
                }
                var player1IsTeam1 = encounterPlayer.isTeam1;
                for(const encounterPlayer of encounter.encounterPlayers) {
                    var player2Id = encounterPlayer.player.id;
                    if(player1Id == player2Id) continue;
                    var countType = (player1IsTeam1 == encounterPlayer.isTeam1) ? 'together' : 'against';
                    if(this.affinity[player1Id][countType][player2Id] === undefined) {
                        this.affinity[player1Id][countType][player2Id] = 0;
                    }
                    this.affinity[player1Id][countType][player2Id]++;
                }
                for(const score of encounter.scores) {
                    this.affinity[player1Id].difference =+ player1IsTeam1 ? score.scoreTeam1 : score.scoreTeam2;
                    this.affinity[player1Id].played =+ score.scoreTeam1 + score.scoreTeam2;
                }
            }
        }
    }

    connect() {
        
    }
}
