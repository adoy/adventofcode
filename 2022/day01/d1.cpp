#include <algorithm>
#include <cstdlib>
#include <iostream>
#include <string>
#include <vector>

using namespace std;

int main() {
  string line;
  vector<unsigned long> elves(1);

  while (getline(cin, line)) {
    if (line.empty()) {
      elves.push_back(0);
      continue;
    }
    elves.back() += stoul(line);
  }
  sort(elves.begin(), elves.end(), greater<unsigned long>());

  cout << "First elf calories: " << elves[0] << endl;
  if (elves.size() >= 3) {
    cout << "First 3 elves calories: " << elves[0] + elves[1] + elves[2] << endl;
  }

  return EXIT_SUCCESS;
}
