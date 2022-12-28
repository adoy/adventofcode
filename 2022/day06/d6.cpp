#include <iostream>
#include <string>
#include <set>

using namespace std;

static int find_sequence(const string &s, int n) {
	size_t length = s.length();
	for (size_t i = 0; i < length - n + 1; i++) {
		set<char> seen;
		for (int j = 0; j < n; j++) {
			seen.insert(s[i+j]);
		}
		if (seen.size() == static_cast<unsigned long int>(n)) {
			return i + n;
		}
	}

	return -1;
}

int main() {
	string line;
	getline(cin, line);

	cout << "Result 1: " << find_sequence(line, 4) << endl;
	cout << "Result 2: " << find_sequence(line, 14) << endl;

	return 0;
}
