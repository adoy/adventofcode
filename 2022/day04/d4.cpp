#include <iostream>
#include <vector>

using namespace std;

class Section {
private:
	int start;
	int end;

public:
	Section(int start, int end): start(start), end(end) {}
	bool contains(const Section &other) const {
		return start <= other.start && end >= other.end;
	}

	bool overlap(const Section &other) const {
		return start <= other.end && end >= other.start;
	}
};


static auto parse_input() {
	string line;
	vector<pair<Section, Section>> pairs;

	while (getline(cin, line)) {
		int start1, end1, start2, end2;
		sscanf(line.c_str(), "%d-%d,%d-%d", &start1, &end1, &start2, &end2);
		pairs.push_back({{start1, end1}, {start2, end2}});
	}

	return pairs;
}

int main() {
	vector<pair<Section, Section>> pairs = parse_input();
	int res1 = 0, res2 = 0;

	for (auto &pair : pairs) {
		if (pair.first.contains(pair.second) || pair.second.contains(pair.first)) {
			res1++;
		}
		if (pair.first.overlap(pair.second) || pair.second.overlap(pair.first)) {
			res2++;
		}
	}

	cout << "Result 1: " << res1 << endl;
	cout << "Result 2: " << res2 << endl;
}
