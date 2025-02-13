// Function to update the player's display information
function updatePlayerDisplay(playerId, data) {
    const playerCard = document.getElementById(`player-${playerId}`);
    if (playerCard) {
        const tokenCount = playerCard.querySelector('.token-count');
        const moneyValue = playerCard.querySelector('.money-value');
        const totalInput = playerCard.querySelector('.total-input');

        if (tokenCount) tokenCount.textContent = data.new_balance;
        if (moneyValue) moneyValue.textContent = (data.new_balance * 5).toFixed(2);
        if (totalInput) totalInput.textContent = data.total_tokens_input;
    }
}

// Function to handle adding tokens to a player
function addTokens(event, playerId) {
    event.preventDefault();
    const tokens = parseInt(event.target.tokens.value);

    fetch('/slot/pages/update_tokens.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            player_id: playerId,
            tokens: tokens
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updatePlayerDisplay(playerId, data);
            event.target.reset();
        } else {
            alert('Error adding tokens: ' + data.error);
        }
    });

    return false;
}

// Function to start a game for a player
function startGame(playerId) {
    if (!confirm('Start game? This will cost 5 tokens.')) return;

    // Deduct 5 tokens first
    fetch('/slot/pages/update_tokens.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            player_id: playerId,
            reward: 0,
            cost: 5,
            action_type: 'game_start'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Proceed with the game only if token deduction was successful
            promptForNumbers(playerId);
        } else {
            alert('Not enough tokens!');
        }
    });
}

// Function to prompt for number inputs
function promptForNumbers(playerId) {
    const numbers = [];
    for (let i = 0; i < 3; i++) {
        let num;
        do {
            num = parseInt(prompt(`Enter number ${i + 1} (1-6):`));
        } while (isNaN(num) || num < 1 || num > 6);
        numbers.push(num);
    }

    calculateReward(playerId, numbers);
}

// Function to calculate the reward based on the numbers
function calculateReward(playerId, numbers) {
    let reward = 0;

    // Sort numbers for easier comparison
    numbers.sort();
    const numberString = numbers.join('');

    // Check for 666
    if (numberString === '666') {
        reward = 1000;
    }
    // Check for three of a kind
    else if (numbers[0] === numbers[1] && numbers[1] === numbers[2]) {
        switch (numbers[0]) {
            case 5: reward = 200; break;
            case 4: reward = 50; break;
            case 3: reward = 25; break;
            case 2: reward = 10; break;
            case 1: reward = 5; break;
        }
    }
    // Count occurrences of 1s and 2s
    else {
        const ones = numbers.filter(n => n === 1).length;
        const twos = numbers.filter(n => n === 2).length;

        if (ones === 1) reward = 1;
        else if (ones === 2) reward = 2;

        if (twos === 1 || twos === 2) reward = 3;
    }

    // Show result and add reward if won
    if (reward > 0) {
        addReward(playerId, reward);
    } else {
        alert('No reward this time! Lost 5 tokens.');
        location.reload();
    }
}

// Function to add reward tokens to a player
function addReward(playerId, reward) {
    fetch('/slot/pages/update_tokens.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            player_id: playerId,
            reward: reward,
            cost: 0,
            action_type: 'game_win'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`You won ${reward} tokens!`);
            location.reload();
        } else {
            alert('Error updating tokens');
        }
    });
}