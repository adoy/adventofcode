#include <iostream>
#include <memory>
#include <vector>
#include <deque>

using namespace std;

class Instruction {
	public:
		int count;
		int from;
		int to;
};

class Stacks {
	public:
		vector<deque<char>> stacks;

	void move(const Instruction &instruction, const bool keepOrder = false) {
		deque<char> tmp;
		for (int i = 0; i < instruction.count && !stacks.at(instruction.from-1).empty(); i++) {
			tmp.push_back(stacks[instruction.from-1].front());
			stacks.at(instruction.from-1).pop_front();
		}
		if (keepOrder) {
			while (!tmp.empty()) {
				stacks.at(instruction.to-1).push_front(tmp.back());
				tmp.pop_back();
			}
		} else {
			while (!tmp.empty()) {
				stacks.at(instruction.to-1).push_front(tmp.front());
				tmp.pop_front();
			}
		}
	}

	string get_top_creates() const {
		string result;
		for (auto const &stack : stacks) {
			result += stack.front();
		}
		return result;
	}
};

static pair<Stacks, vector<Instruction>> parse_input() {
	Stacks stacks;
	vector<Instruction> instructions;
	string line;
	while (getline(cin, line)) {
		if (line == "") {
			break;
		}
		for (long unsigned int i = 1; i < line.length(); i+=4) {
			long unsigned int stackId = i/4;
			if (stacks.stacks.size() <= stackId) {
				stacks.stacks.push_back(deque<char>());
			}
			if (line[i-1] == '[') {
				stacks.stacks.at(stackId).push_back(line[i]);
			}
		}
	}
	while (getline(cin, line)) {
		int count, from, to;
		sscanf(line.c_str(), "move %d from %d to %d", &count, &from, &to);
		instructions.push_back(Instruction{count, from, to});
	}

	return make_pair(stacks, instructions);
}

int main() {

	pair<Stacks, vector<Instruction>> input = parse_input();
	Stacks stacks2 = input.first;

	for (auto const &instruction : input.second) {
		input.first.move(instruction);
	}
	for (auto const &instruction : input.second) {
		stacks2.move(instruction, true);
	}

	cout << "Result 1: " << input.first.get_top_creates() << endl;
	cout << "Result 2: " << stacks2.get_top_creates() << endl;

	return 0;
}
